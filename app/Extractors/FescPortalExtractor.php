<?php

namespace App\Extractors;

use App\Contracts\SourceExtractor;
use App\Enums\ContentSourceKey;
use App\Enums\MediaKind;
use App\Exceptions\SourceExtractionException;
use App\Models\Source;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FescPortalExtractor implements SourceExtractor
{
    /**
     * Meses usados en la columna "Fecha de creación" de los listados Joomla.
     *
     * @var array<string, int>
     */
    private const MONTHS = [
        'enero' => 1,
        'ene' => 1,
        'febrero' => 2,
        'feb' => 2,
        'marzo' => 3,
        'mar' => 3,
        'abril' => 4,
        'abr' => 4,
        'mayo' => 5,
        'may' => 5,
        'junio' => 6,
        'jun' => 6,
        'julio' => 7,
        'jul' => 7,
        'agosto' => 8,
        'ago' => 8,
        'septiembre' => 9,
        'sep' => 9,
        'octubre' => 10,
        'oct' => 10,
        'noviembre' => 11,
        'nov' => 11,
        'diciembre' => 12,
        'dic' => 12,
    ];

    public function sourceKey(): ContentSourceKey
    {
        return ContentSourceKey::Fesc;
    }

    public function extract(Source $source): array
    {
        $discovered = $this->discoverItems($source);

        if ($discovered === []) {
            throw new SourceExtractionException(
                'No se encontraron noticias en el portal FESC (carrusel «Proyectamos Nuestra Institución» o listados de News Bienestar, Comunicados, Novedades SIG y News Extension). El HTML del portal pudo haber cambiado.',
            );
        }

        $limit = max(1, (int) config('ingestion.fesc.item_limit', 12));
        $extracted = [];

        foreach ($this->selectWithinLimit($discovered, $limit) as $item) {
            $article = $this->extractArticle($item, $source);

            if ($article !== null) {
                $extracted[] = $article;
            }
        }

        if ($extracted === []) {
            throw new SourceExtractionException(
                'Se encontraron enlaces de noticias, pero ninguno pudo extraerse con contenido editorial.',
            );
        }

        return $extracted;
    }

    /**
     * @return list<array{origin_url: string, title: string, origin_published_at: ?Carbon, section: string, label: string, category: string}>
     */
    private function discoverItems(Source $source): array
    {
        $byUrl = [];

        foreach ($this->homeCarouselItems($source) as $item) {
            $byUrl[$item['origin_url']] = $item;
        }

        foreach ($this->configuredListings() as $listing) {
            foreach ($this->listingItems($source, $listing) as $item) {
                $existing = $byUrl[$item['origin_url']] ?? null;

                if ($existing === null || ($existing['origin_published_at'] === null && $item['origin_published_at'] !== null)) {
                    $byUrl[$item['origin_url']] = $item;
                }
            }
        }

        return array_values($byUrl);
    }

    /**
     * Reparte el cupo entre las secciones del portal para no dejar fuera
     * Comunicados, Novedades SIG o News Extension cuando el carrusel ya llenó el límite.
     *
     * @param  list<array{origin_url: string, title: string, origin_published_at: ?Carbon, section: string, label: string, category: string}>  $discovered
     * @return list<array{origin_url: string, title: string, origin_published_at: ?Carbon, section: string, label: string, category: string}>
     */
    private function selectWithinLimit(array $discovered, int $limit): array
    {
        if (count($discovered) <= $limit) {
            return $discovered;
        }

        $queues = [];

        foreach ($discovered as $item) {
            $queues[$item['section']][] = $item;
        }

        foreach ($queues as &$queue) {
            usort($queue, function (array $left, array $right): int {
                $leftDate = $left['origin_published_at'];
                $rightDate = $right['origin_published_at'];

                if ($leftDate !== null && $rightDate !== null) {
                    return $rightDate <=> $leftDate;
                }

                if ($leftDate !== null) {
                    return -1;
                }

                if ($rightDate !== null) {
                    return 1;
                }

                return 0;
            });
        }
        unset($queue);

        $selected = [];
        $sectionOrder = array_keys($queues);

        while (count($selected) < $limit) {
            $progress = false;

            foreach ($sectionOrder as $section) {
                if ($queues[$section] === []) {
                    continue;
                }

                $selected[] = array_shift($queues[$section]);
                $progress = true;

                if (count($selected) >= $limit) {
                    break;
                }
            }

            if (! $progress) {
                break;
            }
        }

        return $selected;
    }

    /**
     * @return list<array{path: string, section: string, label: string, category: string}>
     */
    private function configuredListings(): array
    {
        $listings = config('ingestion.fesc.listings', []);

        if (! is_array($listings) || $listings === []) {
            return [[
                'path' => 'comunicados',
                'section' => 'comunicados',
                'label' => 'Comunicados',
                'category' => 'comunicado',
            ]];
        }

        $normalized = [];

        foreach ($listings as $listing) {
            if (! is_array($listing) || ! isset($listing['path'])) {
                continue;
            }

            $path = trim((string) $listing['path'], '/');

            $normalized[] = [
                'path' => $path,
                'section' => (string) ($listing['section'] ?? $path),
                'label' => (string) ($listing['label'] ?? $path),
                'category' => (string) ($listing['category'] ?? 'noticia'),
            ];
        }

        return $normalized;
    }

    /**
     * @return list<array{origin_url: string, title: string, origin_published_at: ?Carbon, section: string, label: string, category: string}>
     */
    private function homeCarouselItems(Source $source): array
    {
        try {
            $html = $this->fetchHtml(
                $this->homeUrl($source),
                'No se pudo leer la portada del portal FESC.',
            );
        } catch (SourceExtractionException $exception) {
            Log::warning('No se pudo extraer el carrusel institucional FESC.', [
                'reason' => $exception->getMessage(),
            ]);

            return [];
        }

        $xpath = $this->xpath($html);
        $links = $xpath->query('//div[contains(@class,"jtcs_item_wrapper")]//a[contains(@class,"jt-title") or contains(@class,"link-image")]');

        if ($links === false || $links->length === 0) {
            return [];
        }

        $items = [];

        foreach ($links as $link) {
            if (! $link instanceof DOMElement) {
                continue;
            }

            $href = trim($link->getAttribute('href'));
            $title = $this->normalizeText($link->getAttribute('title') ?: $link->textContent);
            $listing = $this->listingFromUrl($href);

            if ($href === '' || $title === '' || $listing === null || $this->shouldIgnoreUrl($href)) {
                continue;
            }

            $originUrl = $this->canonicalUrl($this->absoluteUrl($href, $source));

            if (isset($items[$originUrl])) {
                continue;
            }

            $dateNode = $xpath->query('.//ancestor::div[contains(@class,"item") or contains(@class,"slide")][1]//*[contains(@class,"jt-date") or contains(@class,"create")]', $link)?->item(0);

            $items[$originUrl] = [
                'origin_url' => $originUrl,
                'title' => $title,
                'origin_published_at' => $dateNode instanceof DOMNode
                    ? $this->parseSpanishDate($dateNode->textContent)
                    : null,
                'section' => $listing['section'],
                'label' => $listing['label'],
                'category' => $listing['category'],
            ];
        }

        return array_values($items);
    }

    /**
     * @param  array{path: string, section: string, label: string, category: string}  $listing
     * @return list<array{origin_url: string, title: string, origin_published_at: ?Carbon, section: string, label: string, category: string}>
     */
    private function listingItems(Source $source, array $listing): array
    {
        try {
            $html = $this->fetchHtml(
                $this->listingUrl($source, $listing['path']),
                "No se pudo leer el listado {$listing['label']} del portal FESC.",
            );
        } catch (SourceExtractionException $exception) {
            Log::warning('No se pudo extraer un listado FESC.', [
                'listing' => $listing['path'],
                'reason' => $exception->getMessage(),
            ]);

            return [];
        }

        $xpath = $this->xpath($html);
        $rows = $xpath->query('//table[contains(concat(" ", normalize-space(@class), " "), " com-content-category__table ")]//tbody/tr');

        if ($rows === false || $rows->length === 0) {
            return [];
        }

        $items = [];

        foreach ($rows as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }

            $link = $xpath->query('.//th[contains(@class,"list-title")]//a', $row)?->item(0);
            $dateCell = $xpath->query('.//td[contains(@class,"list-date")]', $row)?->item(0);

            if (! $link instanceof DOMElement) {
                continue;
            }

            $href = trim($link->getAttribute('href'));
            $title = $this->normalizeText($link->textContent);

            if ($href === '' || $title === '' || $this->shouldIgnoreUrl($href)) {
                continue;
            }

            $originUrl = $this->canonicalUrl($this->absoluteUrl($href, $source));

            if (! str_contains($originUrl, '/'.$listing['path'].'/')) {
                continue;
            }

            $items[] = [
                'origin_url' => $originUrl,
                'title' => $title,
                'origin_published_at' => $dateCell instanceof DOMNode
                    ? $this->parseSpanishDate($dateCell->textContent)
                    : null,
                'section' => $listing['section'],
                'label' => $listing['label'],
                'category' => $listing['category'],
            ];
        }

        return $items;
    }

    /**
     * @param  array{origin_url: string, title: string, origin_published_at: ?Carbon, section: string, label: string, category: string}  $item
     * @return array<string, mixed>|null
     */
    private function extractArticle(array $item, Source $source): ?array
    {
        try {
            $html = $this->fetchHtml(
                $item['origin_url'],
                'No se pudo leer una noticia del portal FESC.',
            );
        } catch (SourceExtractionException $exception) {
            Log::warning('No se pudo extraer una noticia FESC.', [
                'url' => $item['origin_url'],
                'reason' => $exception->getMessage(),
            ]);

            return null;
        }

        $xpath = $this->xpath($html);
        $headline = $xpath->query('//div[contains(@class,"article-details")]//h1[@itemprop="headline"]')?->item(0);
        $body = $xpath->query('//div[contains(@class,"article-details")]//div[@itemprop="articleBody"]')?->item(0);

        if (! $body instanceof DOMElement) {
            Log::warning('La noticia FESC no tiene cuerpo editorial.', [
                'url' => $item['origin_url'],
            ]);

            return null;
        }

        $title = $headline instanceof DOMNode
            ? $this->normalizeText($headline->textContent)
            : $item['title'];
        $rawHtml = $this->sanitizeHtml($this->innerHtml($body));
        $rawText = $this->normalizeText(html_entity_decode(strip_tags($rawHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($title === '' || $rawText === '') {
            return null;
        }

        $externalId = $this->externalIdFromUrl($item['origin_url']);

        return [
            'origin_url' => $item['origin_url'],
            'external_id' => $externalId,
            'title' => $title,
            'raw_text' => $rawText,
            'raw_html' => $rawHtml,
            'media' => $this->articleMedia($xpath, $source),
            'metadata' => [
                'section' => $item['section'],
                'section_label' => $item['label'],
                'category_slug' => $item['category'],
                'external_id' => $externalId,
            ],
            'origin_published_at' => $item['origin_published_at'],
            'content_hash' => hash('sha256', $item['origin_url'].'|'.$rawText),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function articleMedia(DOMXPath $xpath, Source $source): array
    {
        $queries = [
            '//div[contains(@class,"article-details")]//div[contains(@class,"fotorama")]//img',
            '//div[contains(@class,"article-details")]//div[@itemprop="articleBody"]//img',
        ];
        $media = [];
        $seen = [];

        foreach ($queries as $query) {
            $images = $xpath->query($query);

            if ($images === false) {
                continue;
            }

            foreach ($images as $image) {
                if (! $image instanceof DOMElement) {
                    continue;
                }

                $src = $this->cleanMediaUrl($image->getAttribute('src'), $source);

                if ($src === '' || isset($seen[$src]) || ! $this->isContentImage($src)) {
                    continue;
                }

                $seen[$src] = true;
                $media[] = [
                    'kind' => MediaKind::Image->value,
                    'url' => $src,
                    'alt' => $this->normalizeText($image->getAttribute('alt')) ?: null,
                ];

                if (count($media) >= 12) {
                    return $media;
                }
            }
        }

        return $media;
    }

    private function fetchHtml(string $url, string $errorMessage): string
    {
        $response = $this->http()->get($url);

        if (! $response->successful()) {
            throw new SourceExtractionException($errorMessage.' Código HTTP '.$response->status().'.');
        }

        $body = $response->body();

        if (trim($body) === '') {
            throw new SourceExtractionException($errorMessage.' La respuesta llegó vacía.');
        }

        return $body;
    }

    private function http(): PendingRequest
    {
        return Http::timeout((int) config('ingestion.timeout', 15))
            ->retry((int) config('ingestion.retry_times', 2), (int) config('ingestion.retry_sleep_ms', 500))
            ->withUserAgent((string) config('ingestion.user_agent'))
            ->accept('text/html');
    }

    private function homeUrl(Source $source): string
    {
        return rtrim($source->base_url, '/').'/';
    }

    private function listingUrl(Source $source, string $path): string
    {
        return rtrim($source->base_url, '/').'/'.trim($path, '/');
    }

    /**
     * @return array{path: string, section: string, label: string, category: string}|null
     */
    private function listingFromUrl(string $url): ?array
    {
        foreach ($this->configuredListings() as $listing) {
            if (str_contains($url, '/'.$listing['path'].'/')) {
                return $listing;
            }
        }

        return null;
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new DOMXPath($document);
    }

    private function absoluteUrl(string $href, Source $source): string
    {
        if (Str::startsWith($href, ['http://', 'https://'])) {
            return $href;
        }

        $parts = parse_url($source->base_url) ?: [];
        $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? 'www.fesc.edu.co');

        if (str_starts_with($href, '/')) {
            return $origin.$href;
        }

        return rtrim($source->base_url, '/').'/'.ltrim($href, '/');
    }

    private function canonicalUrl(string $url): string
    {
        $parts = parse_url($url) ?: [];
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? 'www.fesc.edu.co';
        $path = $parts['path'] ?? '/';

        return $scheme.'://'.$host.$path;
    }

    private function externalIdFromUrl(string $url): ?string
    {
        if (preg_match('#/(news-bienestar|comunicados|news-sig|news-extension)/(\d+)(?:-|$)#', $url, $matches) !== 1) {
            return null;
        }

        return $matches[2];
    }

    private function shouldIgnoreUrl(string $href): bool
    {
        return (bool) preg_match('#/(login|component/users|politicas|normatividad|privacidad)#i', $href);
    }

    private function parseSpanishDate(string $value): ?Carbon
    {
        $normalized = $this->normalizeText($value);

        if (preg_match('/(\d{1,2})\s+([A-Za-zÁÉÍÓÚÜáéíóúü]+)\s+(\d{4})/u', $normalized, $matches) !== 1) {
            return null;
        }

        $monthName = mb_strtolower($matches[2], 'UTF-8');
        $month = self::MONTHS[$monthName] ?? null;

        if ($month === null) {
            return null;
        }

        return Carbon::create((int) $matches[3], $month, (int) $matches[1], 0, 0, 0);
    }

    private function sanitizeHtml(string $html): string
    {
        $withoutUnsafe = preg_replace('#<(script|iframe|object|embed|form)[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $withoutHandlers = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $withoutUnsafe) ?? $withoutUnsafe;

        return trim($withoutHandlers);
    }

    private function innerHtml(DOMElement $element): string
    {
        $html = '';

        foreach ($element->childNodes as $child) {
            $html .= $element->ownerDocument?->saveHTML($child) ?? '';
        }

        return $html;
    }

    private function cleanMediaUrl(string $src, Source $source): string
    {
        $withoutFragment = trim(explode('#', $src, 2)[0]);

        if ($withoutFragment === '') {
            return '';
        }

        $absolute = $this->absoluteUrl($withoutFragment, $source);
        $canonical = $this->canonicalUrl($absolute);

        return preg_replace('#(?<!:)/{2,}#', '/', $canonical) ?? $canonical;
    }

    private function isContentImage(string $url): bool
    {
        if (! str_contains($url, '/images/')) {
            return false;
        }

        $ignored = [
            '/logo',
            '/footer/',
            '/red-social/',
            '/wompi/',
            '/boletines/',
            '/inicio/',
            '/modules/',
            'facebook.com/tr',
        ];

        foreach ($ignored as $fragment) {
            if (str_contains($url, $fragment)) {
                return false;
            }
        }

        return true;
    }

    private function normalizeText(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }
}
