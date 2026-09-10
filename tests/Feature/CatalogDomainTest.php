<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\ScrapeRunStatus;
use App\Models\ImportedContent;
use App\Models\News;
use App\Models\NewsView;
use App\Models\NfcPoint;
use App\Models\NfcScan;
use App\Models\Source;
use App\Models\User;
use App\Services\ImportContentService;
use App\Services\NewsLifecycleService;
use App\Services\NfcAssociationService;
use App\Services\ScrapeRunService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class CatalogDomainTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_visitor_can_view_published_news_without_authentication(): void
    {
        $news = News::factory()->published()->create([
            'title' => 'Noticia pública de campus',
            'slug' => 'noticia-publica-campus',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Noticia pública de campus');

        $this->get(route('contents.show', $news->slug))
            ->assertOk()
            ->assertSee('Noticia pública de campus');
    }

    public function test_draft_news_is_not_public(): void
    {
        $news = News::factory()->draft()->create([
            'slug' => 'borrador-oculto',
        ]);

        $this->get(route('contents.show', $news->slug))->assertNotFound();
        $this->get(route('home'))->assertDontSee($news->title);
    }

    public function test_duplicate_import_does_not_create_a_second_record(): void
    {
        $source = Source::factory()->create();
        $service = app(ImportContentService::class);
        $payload = [
            'origin_url' => 'https://www.fesc.edu.co/portal/noticia-unica',
            'external_id' => 'fesc-42',
            'title' => 'Noticia importada',
            'raw_text' => 'Texto extraído',
        ];

        $first = $service->importOrFind($source, $payload);
        $second = $service->importOrFind($source, $payload);
        $draftA = $service->draftFromImport($first);
        $draftB = $service->draftFromImport($second);

        $this->assertTrue($first->is($second));
        $this->assertTrue($draftA->is($draftB));
        $this->assertSame(1, ImportedContent::query()->count());
        $this->assertSame(1, News::query()->count());
        $this->assertSame(ContentStatus::Draft, $draftA->status);
    }

    public function test_scrape_run_is_recorded(): void
    {
        $service = app(ScrapeRunService::class);
        $run = $service->start('manual');
        $service->markCompleted($run, [
            'contents_found' => 4,
            'contents_new' => 2,
            'contents_processed' => 2,
            'news_created' => 2,
        ]);

        $this->assertSame(ScrapeRunStatus::Completed, $run->fresh()->status);
        $this->assertSame(2, $run->fresh()->contents_new);
        $this->assertNotNull($run->fresh()->finished_at);
    }

    public function test_administrator_can_edit_draft_and_publish(): void
    {
        $admin = User::factory()->admin()->create();
        $news = News::factory()->draft()->create([
            'title' => 'Borrador original',
            'slug' => 'borrador-original',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.news.update', $news), [
                'title' => 'Borrador revisado',
                'summary' => 'Resumen corregido por el administrador',
                'body' => 'Cuerpo revisado',
                'category_id' => $news->category_id,
                'origin_url' => 'https://www.fesc.edu.co/portal/',
            ])
            ->assertRedirect(route('admin.news.edit', $news));

        $news->refresh();
        $this->assertSame('Borrador revisado', $news->title);
        $this->assertNotNull($news->admin_edited_at);
        $this->assertSame(ContentStatus::Draft, $news->status);

        $this->actingAs($admin)
            ->post(route('admin.news.publish', $news))
            ->assertRedirect(route('admin.news.index'));

        $this->assertSame(ContentStatus::Published, $news->fresh()->status);
        $this->get(route('contents.show', $news->slug))->assertOk();
    }

    public function test_administrator_can_associate_one_published_news_to_an_nfc_point(): void
    {
        $admin = User::factory()->admin()->create();
        $first = News::factory()->published()->create();
        $second = News::factory()->published()->create();
        $point = NfcPoint::factory()->create(['code' => 'bloque-a']);

        $this->actingAs($admin)
            ->patch(route('admin.nfc.update', $point), [
                'news_id' => $first->id,
            ])
            ->assertRedirect(route('admin.nfc.index'));

        $this->actingAs($admin)
            ->patch(route('admin.nfc.update', $point), [
                'news_id' => $second->id,
            ])
            ->assertRedirect(route('admin.nfc.index'));

        $point->refresh();
        $this->assertSame($second->id, $point->news_id);
        $this->assertSame(1, NfcPoint::query()->where('code', 'bloque-a')->count());
    }

    public function test_nfc_point_cannot_associate_a_draft(): void
    {
        $point = NfcPoint::factory()->create();
        $draft = News::factory()->draft()->create();

        $this->expectException(InvalidArgumentException::class);
        app(NfcAssociationService::class)->associate($point, $draft);
    }

    public function test_public_news_visit_is_recorded(): void
    {
        $news = News::factory()->published()->create(['slug' => 'visita-directa']);

        $this->get(route('contents.show', $news->slug))->assertOk();

        $this->assertSame(1, NewsView::query()->where('news_id', $news->id)->count());
    }

    public function test_nfc_scan_is_recorded_and_differs_from_a_news_visit(): void
    {
        $news = News::factory()->published()->create(['slug' => 'desde-nfc']);
        $point = NfcPoint::factory()->create([
            'code' => 'biblioteca',
            'news_id' => $news->id,
        ]);

        $this->get(route('nfc.show', $point->code))->assertOk();

        $this->assertSame(1, NfcScan::query()->where('nfc_point_id', $point->id)->count());
        $this->assertSame(0, NewsView::query()->count());
    }

    public function test_archived_news_remains_in_history_and_is_not_public(): void
    {
        $news = News::factory()->published()->create(['slug' => 'noticia-historica']);

        app(NewsLifecycleService::class)->archive($news);

        $this->assertDatabaseHas('news', [
            'slug' => 'noticia-historica',
            'status' => ContentStatus::Archived->value,
        ]);
        $this->get(route('contents.show', 'noticia-historica'))->assertNotFound();
    }

    public function test_inactive_nfc_does_not_record_a_scan(): void
    {
        $point = NfcPoint::factory()->inactive()->create(['code' => 'bloque-c']);

        $this->get(route('nfc.show', $point->code))->assertOk();

        $this->assertSame(0, NfcScan::query()->count());
    }

    public function test_administrator_nfc_listing_allows_association(): void
    {
        $admin = User::factory()->admin()->create();
        $news = News::factory()->published()->create([
            'title' => 'Noticia asociable al campus',
        ]);
        $point = NfcPoint::factory()->create([
            'code' => 'entrada-avenida-4',
            'name' => 'Entrada Avenida 4',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.nfc.index'))
            ->assertOk()
            ->assertSee('Entrada Avenida 4')
            ->assertSee('Noticia asociable al campus')
            ->assertSee('Asociar')
            ->assertSee('name="news_id"', false);

        $this->actingAs($admin)
            ->from(route('admin.nfc.index'))
            ->patch(route('admin.nfc.update', $point), [
                'news_id' => $news->id,
            ])
            ->assertRedirect(route('admin.nfc.index'));

        $this->assertSame($news->id, $point->fresh()->news_id);
    }

    public function test_statistics_use_recorded_visits_and_scans(): void
    {
        $admin = User::factory()->admin()->create();
        $news = News::factory()->published()->create([
            'title' => 'Noticia con visitas reales',
        ]);
        $point = NfcPoint::factory()->create([
            'name' => 'Biblioteca Moisés San Juan López',
            'identifier' => 'NFC-004',
        ]);

        NewsView::factory()->count(3)->create(['news_id' => $news->id]);
        NfcScan::factory()->count(2)->create([
            'nfc_point_id' => $point->id,
            'news_id' => $news->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.statistics.index'))
            ->assertOk()
            ->assertSee('Visitas a noticias')
            ->assertSee('Escaneos NFC')
            ->assertSee('Actividad últimos 14 días')
            ->assertSee('Escaneos por punto NFC')
            ->assertSee('3')
            ->assertSee('2')
            ->assertSee('Noticia con visitas reales')
            ->assertSee('3 visitas')
            ->assertSee('Biblioteca Moisés San Juan López')
            ->assertSee('2 escaneos')
            ->assertSee('stats-trend-chart', false);
    }
}
