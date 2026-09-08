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
     * Meses usados en la columna "Fecha de creación" de /portal/comunicados.
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
        $listingUrl = $this->listingUrl($source);
        $html = $this->fetchHtml($listingUrl, 'No se pudo leer el listado de comunicados del portal FESC.');
        $items = $this->listingItems($html, $source);

        if ($items === []) {
            throw new SourceExtractionException(
                'El listado de comunicados FESC no tiene la tabla esperada (com-content-category__table). El HTML del portal pudo haber cambiado.',
            );
        }

        $limit = max(1, (int) config('ingestion.fesc.item_limit', 8));
        $extracted = [];

        foreach (array_slice($items, 0, $limit) as $item) {
            $article = $this->extractArticle($item);

            if ($article !== null) {
                $extracted[] = $article;
            }
        }

        if ($extracted === []) {
            throw new SourceExtractionException(
                'Se encontró el listado de comunicados, pero ninguno pudo extraerse con contenido editorial.',
            );
        }

        return $extracted;
    }

    /**
     * @return list<array{origin_url: string, title: string, origin_published_at: ?Carbon}>
     */
    private function listingItems(string $html, Source $source): array
    {
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

            if (! str_contains($originUrl, '/comunicados/')) {
                continue;
            }

            $items[] = [
                'origin_url' => $originUrl,
                'title' => $title,
                'origin_published_at' => $dateCell instanceof DOMNode
                    ? $this->parseSpanishDate($dateCell->textContent)
                    : null,
            ];
        }

        return $items;
    }

    /**
     * @param  array{origin_url: string, title: string, origin_published_at: ?Carbon}  $item
     * @return array<string, mixed>|null
     */
    private function extractArticle(array $item): ?array
    {
        try {
            $html = $this->fetchHtml(
                $item['origin_url'],
                'No se pudo leer un comunicado del portal FESC.',
            );
        } catch (SourceExtractionException $exception) {
            Log::warning('No se pudo extraer un comunicado FESC.', [
                'url' => $item['origin_url'],
                'reason' => $exception->getMessage(),
            ]);

            return null;
        }

        $xpath = $this->xpath($html);
        $headline = $xpath->query('//div[contains(@class,"article-details")]//h1[@itemprop="headline"]')?->item(0);
        $body = $xpath->query('//div[contains(@class,"article-details")]//div[@itemprop="articleBody"]')?->item(0);

        if (! $body instanceof DOMElement) {
            Log::warning('El comunicado FESC no tiene cuerpo editorial.', [
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
            'media' => $this->articleMedia($xpath),
            'metadata' => [
                'section' => 'comunicados',
                'external_id' => $externalId,
            ],
            'origin_published_at' => $item['origin_published_at'],
            'content_hash' => hash('sha256', $item['origin_url'].'|'.$rawText),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function articleMedia(DOMXPath $xpath): array
    {
        $images = $xpath->query('//div[contains(@class,"article-details")]//div[contains(@class,"fotorama")]//img');
        $media = [];

        if ($images === false) {
            return $media;
        }

        foreach ($images as $image) {
            if (! $image instanceof DOMElement) {
                continue;
            }

            $src = $this->cleanMediaUrl($image->getAttribute('src'));

            if ($src === '' || ! str_contains($src, '/images/comunicados/')) {
                continue;
            }

            $media[] = [
                'kind' => MediaKind::Image->value,
                'url' => $src,
                'alt' => $this->normalizeText($image->getAttribute('alt')) ?: null,
            ];

            if (count($media) >= 5) {
                break;
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

    private function listingUrl(Source $source): string
    {
        $path = trim((string) config('ingestion.fesc.listing_path', 'comunicados'), '/');

        return rtrim($source->base_url, '/').'/'.$path;
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
        if (preg_match('#/comunicados/(\d+)(?:-|$)#', $url, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    private function shouldIgnoreUrl(string $href): bool
    {
        return (bool) preg_match('#/(login|component/users|politicas|normatividad|privacidad)#i', $href);
    }

    private function parseSpanishDate(string $value): ?Carbon
    {
        $normalized = $this->normalizeText($value);

        if (preg_match('/^(\d{1,2})\s+([A-Za-zÁÉÍÓÚÜáéíóúü]+)\s+(\d{4})$/u', $normalized, $matches) !== 1) {
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

    private function cleanMediaUrl(string $src): string
    {
        $withoutFragment = explode('#', $src, 2)[0];

        return trim($withoutFragment);
    }

    private function normalizeText(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }
}
