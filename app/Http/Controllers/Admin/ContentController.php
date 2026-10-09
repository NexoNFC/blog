<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentSourceKey;
use App\Enums\ContentStatus;
use App\Exceptions\AiRewriteException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateNewsRequest;
use App\Models\Category;
use App\Models\News;
use App\Models\ScrapeRun;
use App\Services\CatalogRewriteService;
use App\Services\ContentIngestionService;
use App\Services\NewsLifecycleService;
use App\Support\MediaUrl;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class ContentController extends Controller
{
    public function __construct(
        private NewsLifecycleService $lifecycle,
        private ContentIngestionService $ingestion,
        private CatalogRewriteService $rewriter,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', News::class);

        $contents = $this->paginatedAdminNews();
        $lastRun = ScrapeRun::query()->latest('id')->first();
        $aiConfigured = $this->rewriter->isConfigured();

        if ($request->expectsJson()) {
            return response()->json($this->indexPayload($contents, $lastRun, $aiConfigured));
        }

        return view('admin.contents.index', [
            'contents' => $contents,
            'lastRun' => $lastRun,
            'aiConfigured' => $aiConfigured,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', News::class);

        return view('admin.contents.create');
    }

    public function ingest(Request $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', News::class);

        $run = $this->ingestion->ingest('manual', ContentSourceKey::Fesc);

        if ($run->status->value === 'error') {
            $message = $run->error_message ?? 'Inténtalo de nuevo más tarde.';

            if ($request->expectsJson()) {
                return response()->json([
                    'type' => 'danger',
                    'title' => 'No se pudieron traer las noticias',
                    'message' => $message,
                    'last_run' => $this->serializeRun($run),
                    ...$this->indexPayload($this->paginatedAdminNews(1), $run, $this->rewriter->isConfigured()),
                ], 422);
            }

            return redirect()
                ->route('admin.news.index')
                ->with('alert', [
                    'type' => 'danger',
                    'title' => 'No se pudieron traer las noticias',
                    'message' => $message,
                ]);
        }

        $message = $run->news_created > 0
            ? "Se revisaron {$run->contents_found} piezas. Nuevas: {$run->news_created}. Quedan en borrador hasta que las apruebes."
            : "Se revisaron {$run->contents_found} piezas. Todas ya estaban en el catálogo; no hay borradores nuevos.";

        if (filled($run->error_message)) {
            $message .= ' '.$run->error_message;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'type' => 'success',
                'title' => 'Listo',
                'message' => $message,
                'last_run' => $this->serializeRun($run),
                ...$this->indexPayload($this->paginatedAdminNews(1), $run, $this->rewriter->isConfigured()),
            ]);
        }

        return redirect()
            ->route('admin.news.index')
            ->with('status', $message);
    }

    public function edit(News $news): View
    {
        $this->authorize('update', $news);

        $news->loadMissing('importedContent', 'category');

        return view('admin.contents.edit', [
            'news' => $news,
            'categories' => Category::query()->orderBy('name')->get(),
            'aiConfigured' => $this->rewriter->isConfigured(),
            'presentation' => $news->processed_payload['presentation'] ?? 'original',
            'images' => $this->previewImages($news),
        ]);
    }

    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        $this->authorize('update', $news);

        $this->lifecycle->update($news, $request->safe()->only([
            'title',
            'summary',
            'body',
            'category_id',
            'origin_url',
        ]));

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('status', 'La noticia se actualizó correctamente.');
    }

    public function rewrite(News $news): RedirectResponse
    {
        $this->authorize('update', $news);

        try {
            $this->rewriter->rewrite($news);
        } catch (AiRewriteException $exception) {
            return redirect()
                ->route('admin.news.edit', $news)
                ->with('alert', [
                    'type' => 'danger',
                    'title' => 'No se pudo transcribir con IA',
                    'message' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('status', 'La noticia se transcribió con IA. Revísala antes de publicarla.');
    }

    public function presentOriginal(News $news): RedirectResponse
    {
        $this->authorize('update', $news);

        $this->rewriter->presentOriginal($news);

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('status', 'Se restauró el texto extraído del portal FESC.');
    }

    public function publish(Request $request, News $news): RedirectResponse|JsonResponse
    {
        $this->authorize('publish', $news);

        try {
            $news = $this->lifecycle->publish($news);
        } catch (InvalidArgumentException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 422);
            }

            return redirect()
                ->route('admin.news.index')
                ->with('alert', [
                    'type' => 'danger',
                    'title' => 'No se pudo publicar',
                    'message' => $exception->getMessage(),
                ]);
        }

        $news->loadMissing(['category', 'importedContent']);
        $message = 'La noticia se aprobó y publicó correctamente.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'news' => $this->toAdminArray($news),
            ]);
        }

        return redirect()
            ->route('admin.news.index')
            ->with('status', $message);
    }

    public function destroy(Request $request, News $news): RedirectResponse|JsonResponse
    {
        $this->authorize('archive', $news);

        $this->lifecycle->archive($news);
        $news->loadMissing(['category', 'importedContent']);
        $message = "La noticia «{$news->title}» se desaprobó y permanece en el histórico.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'news' => $this->toAdminArray($news),
            ]);
        }

        return redirect()
            ->route('admin.news.index')
            ->with('status', $message);
    }

    /**
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    private function paginatedAdminNews(?int $page = null): LengthAwarePaginator
    {
        return News::query()
            ->with(['category', 'importedContent'])
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [ContentStatus::Draft->value])
            ->latest('id')
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString()
            ->through(fn (News $news): array => $this->toAdminArray($news));
    }

    /**
     * @param  LengthAwarePaginator<int, array<string, mixed>>  $contents
     * @return array<string, mixed>
     */
    private function indexPayload(LengthAwarePaginator $contents, ?ScrapeRun $lastRun, bool $aiConfigured): array
    {
        return [
            'data' => array_values($contents->items()),
            'meta' => [
                'current_page' => $contents->currentPage(),
                'last_page' => $contents->lastPage(),
                'per_page' => $contents->perPage(),
                'total' => $contents->total(),
            ],
            'last_run' => $this->serializeRun($lastRun),
            'ai_configured' => $aiConfigured,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function toAdminArray(News $news): array
    {
        $urls = [
            'edit' => route('admin.news.edit', $news),
            'publish' => route('admin.news.publish', $news),
            'destroy' => route('admin.news.destroy', $news),
            'show' => null,
        ];

        if ($news->status === ContentStatus::Published) {
            $urls['show'] = route('contents.show', $news);
        }

        return [
            ...$news->toPublicArray(),
            'urls' => $urls,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function serializeRun(?ScrapeRun $run): ?array
    {
        if ($run === null) {
            return null;
        }

        return [
            'id' => $run->id,
            'status' => $run->status->value,
            'contents_found' => $run->contents_found,
            'news_created' => $run->news_created,
            'finished_at' => $run->finished_at?->format('Y-m-d H:i'),
            'error_message' => $run->error_message,
        ];
    }

    /**
     * @return list<string>
     */
    private function previewImages(News $news): array
    {
        $urls = collect($news->importedContent?->media ?? [])
            ->map(fn (mixed $item): mixed => is_array($item) ? ($item['url'] ?? null) : null)
            ->merge($news->gallery ?? [])
            ->prepend($news->featured_image_path)
            ->map(function (mixed $item): ?string {
                $path = is_array($item) ? ($item['url'] ?? null) : $item;

                return is_string($path) ? $path : null;
            })
            ->filter()
            ->unique()
            ->map(fn (string $path): ?string => MediaUrl::resolve($path))
            ->filter()
            ->values();

        return $urls->all();
    }
}
