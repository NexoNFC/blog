<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateNewsRequest;
use App\Models\Category;
use App\Models\News;
use App\Services\NewsLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(private NewsLifecycleService $lifecycle) {}

    public function index(): View
    {
        $this->authorize('viewAny', News::class);

        $contents = News::query()
            ->with('category')
            ->latest()
            ->get()
            ->map(fn (News $news): array => $news->toPublicArray())
            ->all();

        return view('admin.contents.index', [
            'contents' => $contents,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', News::class);

        return view('admin.contents.create');
    }

    public function edit(News $news): View
    {
        $this->authorize('update', $news);

        return view('admin.contents.edit', [
            'news' => $news,
            'categories' => Category::query()->orderBy('name')->get(),
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

    public function publish(News $news): RedirectResponse
    {
        $this->authorize('publish', $news);

        $this->lifecycle->publish($news);

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('status', 'La noticia se publicó correctamente.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->authorize('archive', $news);

        $this->lifecycle->archive($news);

        return redirect()
            ->route('admin.news.index')
            ->with('status', "La noticia «{$news->title}» se archivó y permanece en el histórico.");
    }
}
