<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\ScrapeRunStatus;
use App\Models\News;
use App\Models\ScrapeRun;
use App\Models\Source;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsIngestionAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        config([
            'ingestion.retry_times' => 0,
            'ingestion.timeout' => 5,
            'ingestion.fesc.item_limit' => 8,
            'ai.api_key' => 'test-key',
            'ai.base_url' => 'https://api.deepseek.com',
            'ai.model' => 'deepseek-chat',
        ]);
    }

    public function test_admin_can_ingest_fesc_news_from_the_news_screen(): void
    {
        $this->fakeFescPortal();
        Source::factory()->fesc()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('Traer noticias del portal');

        $this->actingAs($admin)
            ->post(route('admin.news.ingest'))
            ->assertRedirect(route('admin.news.index'));

        $this->assertSame(1, News::query()->where('status', ContentStatus::Draft)->where('origin_url', 'https://www.fesc.edu.co/portal/news-bienestar/1413-reunion-estudiantes')->count());
        $this->assertSame(ScrapeRunStatus::Completed, ScrapeRun::query()->latest('id')->first()?->status);
        $this->get(route('home'))->assertDontSee('Reunión de estudiantes de bienestar');
    }

    public function test_admin_can_rewrite_with_ai_or_restore_original(): void
    {
        $this->fakeFescPortal(withAi: true);
        Source::factory()->fesc()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.news.ingest'));

        $news = News::query()->where('origin_url', 'https://www.fesc.edu.co/portal/news-bienestar/1413-reunion-estudiantes')->first();
        $this->assertNotNull($news);
        $originalBody = (string) $news->body;

        $this->actingAs($admin)
            ->get(route('admin.news.edit', $news))
            ->assertOk()
            ->assertSee('Transcribir con IA')
            ->assertSee('Presentar original')
            ->assertSee('Imágenes extraídas');

        $this->actingAs($admin)
            ->post(route('admin.news.rewrite', $news))
            ->assertRedirect(route('admin.news.edit', $news));

        $news->refresh();
        $this->assertSame('Título transcrito', $news->title);
        $this->assertSame('Cuerpo transcrito para el campus.', $news->body);
        $this->assertSame('ai', $news->processed_payload['presentation'] ?? null);
        $this->assertSame(ContentStatus::Draft, $news->status);

        $this->actingAs($admin)
            ->post(route('admin.news.original', $news))
            ->assertRedirect(route('admin.news.edit', $news));

        $news->refresh();
        $this->assertSame($originalBody, $news->body);
        $this->assertSame('original', $news->processed_payload['presentation'] ?? null);
    }

    public function test_admin_must_publish_before_the_news_is_public(): void
    {
        $this->fakeFescPortal();
        Source::factory()->fesc()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.news.ingest'));

        $news = News::query()->first();
        $this->assertNotNull($news);
        $this->get(route('contents.show', $news->slug))->assertNotFound();

        $this->actingAs($admin)
            ->post(route('admin.news.publish', $news))
            ->assertRedirect(route('admin.news.index'));

        $this->get(route('contents.show', $news->slug))
            ->assertOk()
            ->assertSee($news->title);
    }

    public function test_guest_cannot_trigger_ingestion(): void
    {
        $this->post(route('admin.news.ingest'))->assertRedirect(route('login'));
    }

    private function fakeFescPortal(bool $withAi = false): void
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
            $withAi,
        ) {
            $url = $request->url();

            if (str_contains($url, 'chat/completions')) {
                if (! $withAi) {
                    return Http::response(['error' => 'unavailable'], 500);
                }

                return Http::response([
                    'choices' => [[
                        'message' => [
                            'content' => json_encode([
                                'title' => 'Título transcrito',
                                'summary' => 'Resumen transcrito',
                                'body' => 'Cuerpo transcrito para el campus.',
                            ], JSON_UNESCAPED_UNICODE),
                        ],
                    ]],
                ], 200);
            }

            if (rtrim($url, '/') === 'https://www.fesc.edu.co/portal') {
                return Http::response($home, 200);
            }

            if ($url === 'https://www.fesc.edu.co/portal/comunicados') {
                return Http::response($listing, 200);
            }

            if ($url === 'https://www.fesc.edu.co/portal/news-bienestar') {
                return Http::response($bienestarListing, 200);
            }

            if ($url === 'https://www.fesc.edu.co/portal/news-sig') {
                return Http::response($sigListing, 200);
            }

            if ($url === 'https://www.fesc.edu.co/portal/news-extension') {
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
