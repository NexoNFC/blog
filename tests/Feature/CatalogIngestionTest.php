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
        Http::fake(function () {
            return Http::response('error', 500);
        });
        Source::factory()->fesc()->create();

        $run = app(ContentIngestionService::class)->ingest('manual', ContentSourceKey::Fesc);

        $this->assertSame(ScrapeRunStatus::Error, $run->status);
        $this->assertNotNull($run->error_message);
        $this->assertSame(0, ImportedContent::query()->count());
        $this->assertSame(0, News::query()->count());
    }

    public function test_missing_listing_markup_marks_scrape_run_as_error(): void
    {
        Http::fake(function () {
            return Http::response('<html><body>Inicio</body></html>', 200);
        });
        Source::factory()->fesc()->create();

        $run = app(ContentIngestionService::class)->ingest('manual', ContentSourceKey::Fesc);

        $this->assertSame(ScrapeRunStatus::Error, $run->status);
        $this->assertStringContainsString('No se encontraron noticias', (string) $run->error_message);
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
        $this->post(route('admin.news.ingest'))->assertRedirect(route('login'));
        $this->get('/')->assertOk();
    }

    public function test_fesc_ingestion_reads_home_carousel_and_all_section_listings(): void
    {
        $this->fakeFescPortal(withAllSections: true);
        Source::factory()->fesc()->create();

        $run = app(ContentIngestionService::class)->ingest('manual', ContentSourceKey::Fesc);

        $this->assertSame(ScrapeRunStatus::Completed, $run->status);
        $this->assertSame(5, $run->contents_found);
        $this->assertSame(5, News::query()->where('status', ContentStatus::Draft)->count());

        $bienestar = News::query()->where('origin_url', 'https://www.fesc.edu.co/portal/news-bienestar/1413-reunion-estudiantes')->first();
        $this->assertNotNull($bienestar);
        $this->assertStringContainsString('estudiantes becarios', (string) $bienestar->body);
        $this->assertSame('https://www.fesc.edu.co/portal/images/bienestar/noticias/prev-01.jpg', $bienestar->featured_image_path);
        $this->assertContains('https://www.fesc.edu.co/portal/images/bienestar/noticias/prev-02.jpg', $bienestar->gallery);
        $this->assertSame('News Bienestar', $bienestar->importedContent?->metadata['section_label'] ?? null);
        $this->assertSame('original', $bienestar->processed_payload['presentation'] ?? null);

        $this->assertNotNull(News::query()->where('origin_url', 'https://www.fesc.edu.co/portal/comunicados/1411-mundo-fesc-ratifica-su-clasificacion')->first());
        $this->assertSame('Novedades SIG', News::query()->where('origin_url', 'https://www.fesc.edu.co/portal/news-sig/1409-revision-direccion')->first()?->importedContent?->metadata['section_label'] ?? null);
        $this->assertSame('News Extension', News::query()->where('origin_url', 'https://www.fesc.edu.co/portal/news-extension/1405-diferenciate')->first()?->importedContent?->metadata['section_label'] ?? null);
    }

    private function fakeFescPortal(bool $withAllSections = false): void
    {
        $listing = file_get_contents(base_path('tests/Fixtures/fesc/listing.html'));
        $article = file_get_contents(base_path('tests/Fixtures/fesc/article.html'));
        $secondary = file_get_contents(base_path('tests/Fixtures/fesc/article-secondary.html'));
        $home = file_get_contents(base_path('tests/Fixtures/fesc/home.html'));
        $bienestarListing = file_get_contents(base_path('tests/Fixtures/fesc/listing-bienestar.html'));
        $bienestarArticle = file_get_contents(base_path('tests/Fixtures/fesc/article-bienestar.html'));
        $sigListing = file_get_contents(base_path('tests/Fixtures/fesc/listing-sig.html'));
        $sigArticle = file_get_contents(base_path('tests/Fixtures/fesc/article-sig.html'));
        $extensionListing = file_get_contents(base_path('tests/Fixtures/fesc/listing-extension.html'));
        $extensionArticle = file_get_contents(base_path('tests/Fixtures/fesc/article-extension.html'));

        Http::fake(function ($request) use (
            $listing,
            $article,
            $secondary,
            $home,
            $bienestarListing,
            $bienestarArticle,
            $sigListing,
            $sigArticle,
            $extensionListing,
            $extensionArticle,
            $withAllSections,
        ) {
            $url = $request->url();

            if ($withAllSections && rtrim($url, '/') === 'https://www.fesc.edu.co/portal') {
                return Http::response($home, 200);
            }

            if ($url === 'https://www.fesc.edu.co/portal/comunicados') {
                return Http::response($listing, 200);
            }

            if ($withAllSections && $url === 'https://www.fesc.edu.co/portal/news-bienestar') {
                return Http::response($bienestarListing, 200);
            }

            if ($withAllSections && $url === 'https://www.fesc.edu.co/portal/news-sig') {
                return Http::response($sigListing, 200);
            }

            if ($withAllSections && $url === 'https://www.fesc.edu.co/portal/news-extension') {
                return Http::response($extensionListing, 200);
            }

            if (str_contains($url, '1413-reunion-estudiantes')) {
                return Http::response($bienestarArticle, 200);
            }

            if (str_contains($url, '1409-revision-direccion')) {
                return Http::response($sigArticle, 200);
            }

            if (str_contains($url, '1405-diferenciate')) {
                return Http::response($extensionArticle, 200);
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
