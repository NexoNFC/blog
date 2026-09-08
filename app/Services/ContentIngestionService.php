<?php

namespace App\Services;

use App\Enums\ContentSourceKey;
use App\Exceptions\SourceExtractionException;
use App\Extractors\ExtractorRegistry;
use App\Models\ScrapeRun;
use App\Models\Source;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContentIngestionService
{
    public function __construct(
        private ScrapeRunService $scrapeRuns,
        private ImportContentService $imports,
        private ExtractorRegistry $extractors,
    ) {}

    public function ingest(string $trigger = 'manual', ?ContentSourceKey $sourceKey = null): ScrapeRun
    {
        $run = $this->scrapeRuns->start($trigger);

        try {
            $sources = Source::query()
                ->where('is_active', true)
                ->when(
                    $sourceKey !== null,
                    fn ($query) => $query->where('key', $sourceKey->value),
                )
                ->orderBy('id')
                ->get();

            if ($sources->isEmpty()) {
                return $this->scrapeRuns->markFailed(
                    $run,
                    $sourceKey === null
                        ? 'No hay fuentes activas para ingestión.'
                        : "No hay una fuente activa con clave {$sourceKey->value}.",
                );
            }

            $stats = [
                'contents_found' => 0,
                'contents_new' => 0,
                'contents_processed' => 0,
                'news_created' => 0,
            ];
            $errors = [];
            $extractedAny = false;

            foreach ($sources as $source) {
                $extractor = $this->extractors->for($source->key);

                if ($extractor === null) {
                    continue;
                }

                try {
                    $items = $extractor->extract($source);
                } catch (SourceExtractionException $exception) {
                    $errors[] = $exception->getMessage();
                    Log::warning('Falló la extracción de una fuente.', [
                        'source' => $source->key,
                        'reason' => $exception->getMessage(),
                    ]);

                    continue;
                }

                $extractedAny = true;
                $stats['contents_found'] += count($items);

                foreach ($items as $payload) {
                    $imported = $this->imports->importOrFind($source, $payload);

                    if ($imported->wasRecentlyCreated) {
                        $stats['contents_new']++;
                    }

                    $news = $this->imports->draftFromImport($imported);
                    $stats['contents_processed']++;

                    if ($news->wasRecentlyCreated) {
                        $stats['news_created']++;
                    }
                }
            }

            if (! $extractedAny) {
                return $this->scrapeRuns->markFailed(
                    $run,
                    $errors === []
                        ? 'No hay extractores disponibles para las fuentes activas.'
                        : implode(' ', $errors),
                );
            }

            $completed = $this->scrapeRuns->markCompleted($run, $stats);

            if ($errors !== []) {
                $completed->error_message = implode(' ', $errors);
                $completed->save();
            }

            return $completed->fresh();
        } catch (Throwable $exception) {
            Log::error('La ingestión del catálogo falló.', [
                'reason' => $exception->getMessage(),
            ]);

            return $this->scrapeRuns->markFailed(
                $run,
                'No se pudo completar la ingestión. Inténtalo de nuevo más tarde.',
            );
        }
    }
}
