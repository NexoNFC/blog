<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateNfcPointAssociationRequest;
use App\Models\News;
use App\Models\NfcPoint;
use App\Services\NfcAssociationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use InvalidArgumentException;

class NfcPointController extends Controller
{
    public function __construct(private NfcAssociationService $associations) {}

    public function index(): View
    {
        $this->authorize('viewAny', NfcPoint::class);

        $recentNews = $this->recentPublishedNews(5);

        $points = NfcPoint::query()
            ->with('news')
            ->withCount('scans')
            ->orderBy('identifier')
            ->get()
            ->map(function (NfcPoint $point) use ($recentNews): array {
                $data = $point->toPublicArray();
                $data['news_id'] = $point->news_id;
                $data['news_title'] = $point->news?->title;
                $data['news_options'] = $this->newsOptionsForPoint($point, $recentNews);

                return $data;
            })
            ->all();

        return view('admin.nfc-points.index', [
            'points' => $points,
        ]);
    }

    public function associate(NfcPoint $nfcPoint): View
    {
        $this->authorize('update', $nfcPoint);

        $nfcPoint->load('news');

        $news = News::query()
            ->published()
            ->orderByDesc('origin_published_at')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get(['id', 'title', 'summary', 'origin_published_at', 'published_at']);

        return view('admin.nfc-points.associate', [
            'point' => $nfcPoint,
            'news' => $news,
        ]);
    }

    public function update(UpdateNfcPointAssociationRequest $request, NfcPoint $nfcPoint): RedirectResponse
    {
        $this->authorize('update', $nfcPoint);

        $newsId = $request->validated('news_id');
        $news = $newsId !== null ? News::query()->findOrFail($newsId) : null;

        try {
            $this->associations->associate($nfcPoint, $news);
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['news_id' => $exception->getMessage()]);
        }

        $returnTo = $request->input('_return');

        if (is_string($returnTo) && str_starts_with($returnTo, url('/admin/nfc'))) {
            return redirect()
                ->to($returnTo)
                ->with('status', 'La asociación del punto NFC se actualizó correctamente.');
        }

        return redirect()
            ->route('admin.nfc.index')
            ->with('status', 'La asociación del punto NFC se actualizó correctamente.');
    }

    /**
     * @return Collection<int, News>
     */
    private function recentPublishedNews(int $limit): Collection
    {
        return News::query()
            ->published()
            ->orderByDesc('origin_published_at')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'title', 'slug']);
    }

    /**
     * @param  Collection<int, News>  $recentNews
     * @return Collection<int, News>
     */
    private function newsOptionsForPoint(NfcPoint $point, Collection $recentNews): Collection
    {
        $options = $recentNews->values();

        if ($point->news && ! $options->contains(fn (News $item): bool => (int) $item->id === (int) $point->news_id)) {
            $options = $options
                ->take(4)
                ->prepend($point->news)
                ->values();
        }

        return $options;
    }
}
