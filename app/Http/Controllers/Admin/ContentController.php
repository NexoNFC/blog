<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentSourceKey;
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
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(
        private NewsLifecycleService $lifecycle,
        private ContentIngestionService $ingestion,
        private CatalogRewriteService $rewriter,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', News::class);

        $contents = News::query()
            ->with(['category', 'importedContent'])
            ->latest()
            ->get()
            ->map(fn (News $news): array => $news->toPublicArray())
            ->all();

        return view('admin.contents.index', [
            'contents' => $contents,
            'lastRun' => ScrapeRun::query()->latest('id')->first(),
            'aiConfigured' => $this->rewriter->isConfigured(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', News::class);

        return view('admin.contents.create');
    }

    public function ingest(): RedirectResponse
    {
        $this->authorize('create', News::class);

        $run = $this->ingestion->ingest('manual', ContentSourceKey::Fesc);

        if ($run->status->value === 'error') {
            return redirect()
                ->route('admin.news.index')
                ->with('alert', [
                    'type' => 'danger',
                    'title' => 'No se pudieron traer las noticias',
                    'message' => $run->error_message ?? 'Inténtalo de nuevo más tarde.',
                ]);
        }

        $message = "Se revisaron {$run->contents_found} piezas. Nuevas: {$run->news_created}. Quedan en borrador hasta que las apruebes.";

        if (filled($run->error_message)) {
            $message .= ' '.$run->error_message;
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

    public function publish(News $news): RedirectResponse
    {
        $this->authorize('publish', $news);

        $this->lifecycle->publish($news);

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'La noticia se aprobó y publicó correctamente.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->authorize('archive', $news);

        $this->lifecycle->archive($news);

        return redirect()
            ->route('admin.news.index')
            ->with('status', "La noticia «{$news->title}» se desaprobó y permanece en el histórico.");
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
