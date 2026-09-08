<?php

namespace Tests\Feature;

use App\Enums\ContentSourceKey;
use App\Enums\ContentStatus;
use App\Enums\ScrapeRunStatus;
use App\Models\ImportedContent;
use App\Models\News;
use App\Models\ScrapeRun;
use App\Models\Source;
use App\Services\ContentIngestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CatalogIngestionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ingestion.retry_times' => 0,
            'ingestion.timeout' => 5,
            'ingestion.fesc.item_limit' => 8,
        ]);
    }

    public function test_fesc_ingestion_creates_imported_content_and_draft_news(): void
    {
        $this->fakeFescPortal();
        Source::factory()->fesc()->create();

        $run = app(ContentIngestionService::class)->ingest('manual', ContentSourceKey::Fesc);

        $this->assertSame(ScrapeRunStatus::Completed, $run->status);
        $this->assertSame('manual', $run->trigger);
        $this->assertSame(2, $run->contents_found);
        $this->assertSame(2, $run->contents_new);
        $this->assertSame(2, $run->contents_processed);
        $this->assertSame(2, $run->news_created);
        $this->assertSame(2, ImportedContent::query()->count());
        $this->assertSame(2, News::query()->count());
        $this->assertSame(2, News::query()->where('status', ContentStatus::Draft)->count());

        $news = News::query()->where('origin_url', 'https://www.fesc.edu.co/portal/comunicados/1411-mundo-fesc-ratifica-su-clasificacion')->first();
        $this->assertNotNull($news);
        $this->assertSame(ContentStatus::Draft, $news->status);
        $this->assertNull($news->published_at);
        $this->assertStringContainsString('Publindex', $news->title);
        $this->assertStringContainsString('Mundo FESC', (string) $news->body);
        $this->assertStringNotContainsString('<iframe', (string) $news->importedContent?->raw_html);
        $this->assertStringNotContainsString('<script', (string) $news->importedContent?->raw_html);
        $this->assertSame('imagen', $news->importedContent?->media[0]['kind'] ?? null);
    }

    public function test_repeating_the_same_fesc_html_does_not_duplicate_records(): void
    {
        $this->fakeFescPortal();
        Source::factory()->fesc()->create();
        $service = app(ContentIngestionService::class);

        $service->ingest('manual', ContentSourceKey::Fesc);
        $service->ingest('manual', ContentSourceKey::Fesc);

        $this->assertSame(2, ImportedContent::query()->count());
        $this->assertSame(2, News::query()->count());
        $this->assertSame(2, ScrapeRun::query()->count());

        $second = ScrapeRun::query()->latest('id')->first();
        $this->assertSame(0, $second?->contents_new);
        $this->assertSame(0, $second?->news_created);
        $this->assertSame(2, $second?->contents_processed);
    }

    public function test_ingested_drafts_are_not_public(): void
    {
        $this->fakeFescPortal();
        Source::factory()->fesc()->create();

        app(ContentIngestionService::class)->ingest('manual', ContentSourceKey::Fesc);

        $news = News::query()->first();
        $this->assertNotNull($news);

        $sentBeforePublicVisit = Http::recorded()->count();

        $this->get(route('contents.show', $news->slug))->assertNotFound();
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee($news->title);

        $this->assertSame($sentBeforePublicVisit, Http::recorded()->count());
    }

    public function test_http_error_on_listing_marks_scrape_run_as_error(): void
    {
        Http::fake([
            'https://www.fesc.edu.co/portal/comunicados' => Http::response('error', 500),
        ]);
        Source::factory()->fesc()->create();

        $run = app(ContentIngestionService::class)->ingest('manual', ContentSourceKey::Fesc);

        $this->assertSame(ScrapeRunStatus::Error, $run->status);
        $this->assertNotNull($run->error_message);
        $this->assertSame(0, ImportedContent::query()->count());
        $this->assertSame(0, News::query()->count());
    }

    public function test_missing_listing_markup_marks_scrape_run_as_error(): void
    {
        Http::fake([
            'https://www.fesc.edu.co/portal/comunicados' => Http::response('<html><body>Inicio</body></html>', 200),
        ]);
        Source::factory()->fesc()->create();

        $run = app(ContentIngestionService::class)->ingest('manual', ContentSourceKey::Fesc);

        $this->assertSame(ScrapeRunStatus::Error, $run->status);
        $this->assertStringContainsString('com-content-category__table', (string) $run->error_message);
    }

    public function test_artisan_command_ingests_fesc_source(): void
    {
        $this->fakeFescPortal();
        Source::factory()->fesc()->create();

        $this->artisan('catalog:ingest', ['--source' => 'fesc', '--trigger' => 'manual'])
            ->assertSuccessful();

        $this->assertSame(1, ScrapeRun::query()->where('status', ScrapeRunStatus::Completed)->count());
        $this->assertSame(2, News::query()->where('status', ContentStatus::Draft)->count());
    }

    public function test_visitor_has_no_ingestion_route(): void
    {
        $this->post('/admin/ingestion')->assertNotFound();
        $this->get('/')->assertOk();
    }

    private function fakeFescPortal(): void
    {
        $listing = file_get_contents(base_path('tests/Fixtures/fesc/listing.html'));
        $article = file_get_contents(base_path('tests/Fixtures/fesc/article.html'));
        $secondary = file_get_contents(base_path('tests/Fixtures/fesc/article-secondary.html'));

        Http::fake(function ($request) use ($listing, $article, $secondary) {
            $url = $request->url();

            if ($url === 'https://www.fesc.edu.co/portal/comunicados') {
                return Http::response($listing, 200);
            }

            if (str_contains($url, '1411-mundo-fesc-ratifica-su-clasificacion')) {
                return Http::response($article, 200);
            }

            if (str_contains($url, '1407-mundo-fesc-escala-en-publindex')) {
                return Http::response($secondary, 200);
            }

            return Http::response('not found', 404);
        });
    }
}
