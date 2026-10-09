<?php

namespace App\Support;

use InvalidArgumentException;
use RuntimeException;

/**
 * Genera texturas equirectangular 2:1 a partir de fotos del campus.
 * La franja ecuatorial envuelve 360°; cielo y suelo rellenan zenit/nadir.
 */
class TourPanoramaBuilder
{
    /**
     * @param  array{width?: int, height?: int, band_ratio?: float, quality?: int}  $options
     */
    public static function build(string $sourcePath, string $destinationPath, array $options = []): void
    {
        if (! is_file($sourcePath)) {
            throw new InvalidArgumentException("Fuente inexistente: {$sourcePath}");
        }

        $width = max(1024, (int) ($options['width'] ?? 3072));
        $height = max(512, (int) ($options['height'] ?? (int) ($width / 2)));
        $bandRatio = min(0.72, max(0.48, (float) ($options['band_ratio'] ?? 0.58)));
        $quality = min(95, max(75, (int) ($options['quality'] ?? 90)));

        $source = self::loadImage($sourcePath);
        $source = self::downscaleIfNeeded($source, 2200);

        $srcW = imagesx($source);
        $srcH = imagesy($source);

        [$sky, $mid, $ground] = self::sampleAtmosphere($source);

        $canvas = imagecreatetruecolor($width, $height);
        if ($canvas === false) {
            throw new RuntimeException('No se pudo crear el lienzo panorámico.');
        }

        self::fillAtmosphere($canvas, $sky, $mid, $ground);

        // Siempre cubrir todo el ancho (envolvente 360° sin espejos raros).
        // Si hace falta, se recorta un poco arriba/abajo de la foto fuente.
        $maxBandH = (int) round($height * max($bandRatio, 0.72));
        $scale = $width / $srcW;
        $scaledH = (int) round($srcH * $scale);
        $srcX = 0;
        $srcY = 0;
        $useSrcW = $srcW;
        $useSrcH = $srcH;

        if ($scaledH > $maxBandH) {
            $useSrcH = max(1, (int) round($srcH * ($maxBandH / $scaledH)));
            $srcY = (int) (($srcH - $useSrcH) / 2);
            $scaledH = $maxBandH;
        }

        // Retratos: llenar alto de franja y extender lados con blur del borde (no espejo duro).
        if ($srcH > $srcW * 1.05) {
            $scaledH = $maxBandH;
            $scale = $scaledH / $srcH;
            $scaledW = max(1, (int) round($srcW * $scale));
            $band = imagecreatetruecolor($width, $scaledH);
            if ($band === false) {
                throw new RuntimeException('No se pudo crear la franja panorámica.');
            }
            $fill = imagecolorallocate($band, $mid[0], $mid[1], $mid[2]);
            imagefilledrectangle($band, 0, 0, $width - 1, $scaledH - 1, $fill);
            $dstX = (int) (($width - $scaledW) / 2);
            imagecopyresampled($band, $source, $dstX, 0, 0, 0, $scaledW, $scaledH, $srcW, $srcH);
            self::edgeStretchSides($band, $dstX, $scaledW);
        } else {
            $band = imagecreatetruecolor($width, $scaledH);
            if ($band === false) {
                throw new RuntimeException('No se pudo crear la franja panorámica.');
            }
            imagecopyresampled($band, $source, 0, 0, $srcX, $srcY, $width, $scaledH, $useSrcW, $useSrcH);
        }

        imagefilter($band, IMG_FILTER_CONTRAST, -10);
        imagefilter($band, IMG_FILTER_COLORIZE, 6, 1, 0, 0);

        $bandY = (int) (($height - $scaledH) / 2);
        $feather = max(12, (int) round($scaledH * 0.07));

        self::blitBandWithFeather($canvas, $band, 0, $bandY, $feather);
        self::blendHorizontalSeam($canvas, 28);
        self::applyVignette($canvas, 0.16);

        $directory = dirname($destinationPath);
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException("No se pudo crear el directorio: {$directory}");
        }

        if (! imagejpeg($canvas, $destinationPath, $quality)) {
            throw new RuntimeException("No se pudo guardar: {$destinationPath}");
        }
    }

    /**
     * @return array<string, string> sourceName => relative public path
     */
    public static function demoManifest(): array
    {
        return [
            'entrada.jpg' => 'images/tours/EntradaAvenida5.jpg',
            'entrada-2.jpg' => 'images/tours/SalidaFesc.jpeg',
            'bloque-a.jpg' => 'images/tours/BloqueB.jpeg',
            'bloque-c.jpg' => 'images/tours/BloqueC.jpg',
            'auditorio.jpg' => 'images/tours/AuditorioAvenida5.jpg',
            'recepcion.jpg' => 'images/tours/Cafeteria.jpeg',
            'cancha.jpg' => 'images/tours/Cancha.jpeg',
        ];
    }

    /**
     * @return list<array{from: string, to: string}>
     */
    public static function demoAliases(): array
    {
        return [
            ['from' => 'images/tours/EntradaAvenida5.jpg', 'to' => 'images/tours/PasilloSalida.jpeg'],
            ['from' => 'images/tours/BloqueC.jpg', 'to' => 'images/tours/DireccionDeSoftware.jpeg'],
        ];
    }

    private static function loadImage(string $path): \GdImage
    {
        $info = @getimagesize($path);
        if ($info === false) {
            throw new InvalidArgumentException("Imagen no legible: {$path}");
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            default => false,
        };

        if ($image === false) {
            throw new InvalidArgumentException("Formato no soportado: {$path}");
        }

        return $image;
    }

    private static function downscaleIfNeeded(\GdImage $source, int $maxWidth): \GdImage
    {
        $srcW = imagesx($source);
        $srcH = imagesy($source);

        if ($srcW <= $maxWidth) {
            return $source;
        }

        $newW = $maxWidth;
        $newH = max(1, (int) round($srcH * ($newW / $srcW)));
        $scaled = imagecreatetruecolor($newW, $newH);

        if ($scaled === false) {
            return $source;
        }

        imagecopyresampled($scaled, $source, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

        return $scaled;
    }

    /**
     * @return array{0: array{int,int,int}, 1: array{int,int,int}, 2: array{int,int,int}}
     */
    private static function sampleAtmosphere(\GdImage $source): array
    {
        $w = imagesx($source);
        $h = imagesy($source);

        $sky = self::averageRegion($source, 0, 0, $w, max(1, (int) ($h * 0.18)));
        $mid = self::averageRegion($source, 0, (int) ($h * 0.40), $w, max(1, (int) ($h * 0.20)));
        $ground = self::averageRegion($source, 0, (int) ($h * 0.82), $w, max(1, (int) ($h * 0.18)));

        // Empuja cielo un poco más claro/azul y suelo más oscuro para profundidad.
        $sky = [
            min(255, (int) ($sky[0] * 0.85 + 40)),
            min(255, (int) ($sky[1] * 0.90 + 55)),
            min(255, (int) ($sky[2] * 0.75 + 90)),
        ];
        $ground = [
            max(12, (int) ($ground[0] * 0.55)),
            max(12, (int) ($ground[1] * 0.55)),
            max(12, (int) ($ground[2] * 0.55)),
        ];

        return [$sky, $mid, $ground];
    }

    /**
     * @return array{int,int,int}
     */
    private static function averageRegion(\GdImage $image, int $x, int $y, int $w, int $h): array
    {
        $imgW = imagesx($image);
        $imgH = imagesy($image);
        $x = max(0, min($imgW - 1, $x));
        $y = max(0, min($imgH - 1, $y));
        $w = max(1, min($imgW - $x, $w));
        $h = max(1, min($imgH - $y, $h));

        $stepX = max(1, (int) floor($w / 48));
        $stepY = max(1, (int) floor($h / 32));
        $r = $g = $b = $n = 0;

        for ($yy = $y; $yy < $y + $h; $yy += $stepY) {
            for ($xx = $x; $xx < $x + $w; $xx += $stepX) {
                $rgb = imagecolorat($image, $xx, $yy);
                $r += ($rgb >> 16) & 0xFF;
                $g += ($rgb >> 8) & 0xFF;
                $b += $rgb & 0xFF;
                $n++;
            }
        }

        if ($n === 0) {
            return [120, 120, 120];
        }

        return [(int) round($r / $n), (int) round($g / $n), (int) round($b / $n)];
    }

    /**
     * @param  array{int,int,int}  $sky
     * @param  array{int,int,int}  $mid
     * @param  array{int,int,int}  $ground
     */
    private static function fillAtmosphere(\GdImage $canvas, array $sky, array $mid, array $ground): void
    {
        $width = imagesx($canvas);
        $height = imagesy($canvas);

        for ($y = 0; $y < $height; $y++) {
            $t = $y / max(1, $height - 1);

            if ($t < 0.45) {
                $u = $t / 0.45;
                $r = (int) round($sky[0] + ($mid[0] - $sky[0]) * $u);
                $g = (int) round($sky[1] + ($mid[1] - $sky[1]) * $u);
                $b = (int) round($sky[2] + ($mid[2] - $sky[2]) * $u);
            } else {
                $u = ($t - 0.45) / 0.55;
                $r = (int) round($mid[0] + ($ground[0] - $mid[0]) * $u);
                $g = (int) round($mid[1] + ($ground[1] - $mid[1]) * $u);
                $b = (int) round($mid[2] + ($ground[2] - $mid[2]) * $u);
            }

            $color = imagecolorallocate($canvas, $r, $g, $b);
            imageline($canvas, 0, $y, $width - 1, $y, $color);
        }
    }

    private static function edgeStretchSides(\GdImage $band, int $contentX, int $contentW): void
    {
        $width = imagesx($band);
        $height = imagesy($band);
        $left = max(0, $contentX);
        $right = min($width, $contentX + $contentW);

        // Extiende el color del borde (no espejo de fachada).
        for ($x = 0; $x < $left; $x++) {
            imagecopy($band, $band, $x, 0, $left, 0, 1, $height);
        }

        for ($x = $right; $x < $width; $x++) {
            imagecopy($band, $band, $x, 0, $right - 1, 0, 1, $height);
        }
    }

    private static function blitBandWithFeather(\GdImage $canvas, \GdImage $band, int $x, int $y, int $feather): void
    {
        $bandW = imagesx($band);
        $bandH = imagesy($band);
        $feather = min($feather, (int) floor($bandH / 3));

        // Centro sólido (rápido).
        $solidY = $feather;
        $solidH = max(0, $bandH - (2 * $feather));
        if ($solidH > 0) {
            imagecopy($canvas, $band, $x, $y + $solidY, 0, $solidY, $bandW, $solidH);
        }

        // Solo difuminar bordes superior e inferior.
        for ($by = 0; $by < $feather; $by++) {
            $fadeTop = $by / max(1, $feather - 1);
            $fadeTop = $fadeTop * $fadeTop * (3 - 2 * $fadeTop);
            $fadeBottom = $fadeTop;

            for ($bx = 0; $bx < $bandW; $bx++) {
                self::blendPixel($canvas, $x + $bx, $y + $by, $band, $bx, $by, $fadeTop);
                self::blendPixel(
                    $canvas,
                    $x + $bx,
                    $y + $bandH - 1 - $by,
                    $band,
                    $bx,
                    $bandH - 1 - $by,
                    $fadeBottom,
                );
            }
        }
    }

    private static function blendPixel(
        \GdImage $canvas,
        int $dstX,
        int $dstY,
        \GdImage $band,
        int $srcX,
        int $srcY,
        float $fade,
    ): void {
        if ($dstX < 0 || $dstY < 0 || $dstX >= imagesx($canvas) || $dstY >= imagesy($canvas)) {
            return;
        }

        $src = imagecolorat($band, $srcX, $srcY);
        $dst = imagecolorat($canvas, $dstX, $dstY);
        $sr = ($src >> 16) & 0xFF;
        $sg = ($src >> 8) & 0xFF;
        $sb = $src & 0xFF;
        $dr = ($dst >> 16) & 0xFF;
        $dg = ($dst >> 8) & 0xFF;
        $db = $dst & 0xFF;
        $nr = (int) round($dr + ($sr - $dr) * $fade);
        $ng = (int) round($dg + ($sg - $dg) * $fade);
        $nb = (int) round($db + ($sb - $db) * $fade);
        imagesetpixel($canvas, $dstX, $dstY, imagecolorallocate($canvas, $nr, $ng, $nb));
    }

    private static function blendHorizontalSeam(\GdImage $canvas, int $seam): void
    {
        $width = imagesx($canvas);
        $height = imagesy($canvas);
        $seam = min($seam, (int) ($width / 8));

        for ($x = 0; $x < $seam; $x++) {
            $t = $x / max(1, $seam - 1);
            $mix = 0.45 * (1 - $t);
            for ($y = 0; $y < $height; $y++) {
                self::mixPixel($canvas, $x, $y, $width - 1 - $x, $y, $mix);
                self::mixPixel($canvas, $width - 1 - $x, $y, $x, $y, $mix);
            }
        }
    }

    private static function applyVignette(\GdImage $canvas, float $strength): void
    {
        $width = imagesx($canvas);
        $height = imagesy($canvas);
        $cx = ($width - 1) / 2;
        $cy = ($height - 1) / 2;
        $max = sqrt($cx * $cx + $cy * $cy);

        for ($y = 0; $y < $height; $y += 2) {
            for ($x = 0; $x < $width; $x += 2) {
                $dx = ($x - $cx) / $max;
                $dy = ($y - $cy) / $max;
                $d = sqrt($dx * $dx + $dy * $dy);
                $factor = 1 - $strength * max(0, $d - 0.35) / 0.65;
                $factor = max(0.55, min(1, $factor));

                foreach ([[0, 0], [1, 0], [0, 1], [1, 1]] as [$ox, $oy]) {
                    $px = $x + $ox;
                    $py = $y + $oy;
                    if ($px >= $width || $py >= $height) {
                        continue;
                    }
                    $rgb = imagecolorat($canvas, $px, $py);
                    $r = (int) ((($rgb >> 16) & 0xFF) * $factor);
                    $g = (int) ((($rgb >> 8) & 0xFF) * $factor);
                    $b = (int) (($rgb & 0xFF) * $factor);
                    imagesetpixel($canvas, $px, $py, imagecolorallocate($canvas, $r, $g, $b));
                }
            }
        }
    }

    private static function mixPixel(\GdImage $image, int $x, int $y, int $sx, int $sy, float $amount): void
    {
        $width = imagesx($image);
        $height = imagesy($image);
        if ($x < 0 || $y < 0 || $sx < 0 || $sy < 0 || $x >= $width || $y >= $height || $sx >= $width || $sy >= $height) {
            return;
        }

        $a = imagecolorat($image, $x, $y);
        $b = imagecolorat($image, $sx, $sy);
        $ar = ($a >> 16) & 0xFF;
        $ag = ($a >> 8) & 0xFF;
        $ab = $a & 0xFF;
        $br = ($b >> 16) & 0xFF;
        $bg = ($b >> 8) & 0xFF;
        $bb = $b & 0xFF;
        $nr = (int) round($ar + ($br - $ar) * $amount);
        $ng = (int) round($ag + ($bg - $ag) * $amount);
        $nb = (int) round($ab + ($bb - $ab) * $amount);
        imagesetpixel($image, $x, $y, imagecolorallocate($image, $nr, $ng, $nb));
    }
}
