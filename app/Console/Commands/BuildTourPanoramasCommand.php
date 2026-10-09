<?php

namespace App\Console\Commands;

use App\Support\TourPanoramaBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

class BuildTourPanoramasCommand extends Command
{
    protected $signature = 'tours:build-panoramas
        {--source= : Carpeta con fotos fuente (default: storage/app/tour-sources)}
        {--width=3072 : Ancho equirectangular (alto = ancho/2)}';

    protected $description = 'Genera panorámicas 2:1 para el viewer 360° a partir de fotos del campus FESC';

    public function handle(): int
    {
        ini_set('memory_limit', '512M');

        $sourceDir = $this->option('source')
            ? (string) $this->option('source')
            : storage_path('app/tour-sources');

        $width = max(2048, (int) $this->option('width'));
        $built = 0;

        foreach (TourPanoramaBuilder::demoManifest() as $sourceName => $relativeTarget) {
            $sourcePath = $sourceDir.DIRECTORY_SEPARATOR.$sourceName;
            $targetPath = public_path($relativeTarget);

            if (! File::isFile($sourcePath)) {
                $this->warn("Fuente ausente, se omite: {$sourceName}");

                continue;
            }

            try {
                $this->line("Generando {$relativeTarget}…");
                TourPanoramaBuilder::build($sourcePath, $targetPath, [
                    'width' => $width,
                    'height' => (int) ($width / 2),
                    'band_ratio' => 0.76,
                    'quality' => 90,
                ]);
                $this->info('  OK '.File::size($targetPath).' bytes');
                $built++;
            } catch (Throwable $e) {
                $this->error("  Falló {$sourceName}: ".$e->getMessage());
            }
        }

        foreach (TourPanoramaBuilder::demoAliases() as $alias) {
            $from = public_path($alias['from']);
            $to = public_path($alias['to']);

            if (File::isFile($from)) {
                File::copy($from, $to);
                $this->line("Alias {$alias['to']}");
            }
        }

        $this->newLine();
        $this->info("Panorámicas listas: {$built}");

        return $built > 0 ? self::SUCCESS : self::FAILURE;
    }
}
