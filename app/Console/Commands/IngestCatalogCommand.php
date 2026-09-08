<?php

namespace App\Console\Commands;

use App\Enums\ContentSourceKey;
use App\Services\ContentIngestionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('catalog:ingest {--source= : Clave de la fuente (fesc). Si se omite, usa las fuentes con extractor.} {--trigger=manual : Origen de la ejecución (manual o scheduled)}')]
#[Description('Importa contenidos recientes desde fuentes oficiales y los deja como borrador.')]
class IngestCatalogCommand extends Command
{
    public function handle(ContentIngestionService $ingestion): int
    {
        $sourceOption = $this->option('source');
        $trigger = (string) $this->option('trigger');

        if (! in_array($trigger, ['manual', 'scheduled'], true)) {
            $this->error('El origen debe ser manual o scheduled.');

            return self::FAILURE;
        }

        $sourceKey = null;

        if (is_string($sourceOption) && $sourceOption !== '') {
            $sourceKey = ContentSourceKey::tryFrom($sourceOption);

            if ($sourceKey === null) {
                $this->error('La fuente indicada no está permitida.');

                return self::FAILURE;
            }
        }

        $run = $ingestion->ingest($trigger, $sourceKey);

        $this->line("ScrapeRun #{$run->id} → {$run->status->value}");
        $this->line("Encontrados: {$run->contents_found}");
        $this->line("Nuevos: {$run->contents_new}");
        $this->line("Procesados: {$run->contents_processed}");
        $this->line("Noticias creadas: {$run->news_created}");

        if ($run->error_message) {
            $this->warn($run->error_message);
        }

        return $run->status->value === 'error' ? self::FAILURE : self::SUCCESS;
    }
}
