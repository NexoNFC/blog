<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateNfcPointAssociationRequest;
use App\Models\News;
use App\Models\NfcPoint;
use App\Services\NfcAssociationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->map(fn (NfcPoint $point): array => $this->toAdminArray($point, $recentNews))
            ->all();

        return view('admin.nfc-points.index', [
            'points' => $points,
        ]);
    }

    public function associate(Request $request, NfcPoint $nfcPoint): View|JsonResponse
    {
        $this->authorize('update', $nfcPoint);

        $nfcPoint->load('news');

        $query = $request->expectsJson()
            ? trim((string) $request->query('q', ''))
            : '';
        $page = $request->expectsJson()
            ? max(1, (int) $request->query('page', 1))
            : 1;

        $news = $this->paginatedPublishedNewsForAssociation($nfcPoint, $query, $page);

        if ($request->expectsJson()) {
            return response()->json($this->associatePayload($nfcPoint, $news, $query));
        }

        return view('admin.nfc-points.associate', [
            'point' => $nfcPoint,
            'associateConfig' => $this->associatePayload($nfcPoint, $news, $query),
        ]);
    }

    public function update(UpdateNfcPointAssociationRequest $request, NfcPoint $nfcPoint): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $nfcPoint);

        $newsId = $request->validated('news_id');
        $news = $newsId !== null ? News::query()->findOrFail($newsId) : null;

        try {
            $this->associations->associate($nfcPoint, $news);
        } catch (InvalidArgumentException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => [
                        'news_id' => [$exception->getMessage()],
                    ],
                ], 422);
            }

            return back()->withErrors(['news_id' => $exception->getMessage()]);
        }

        $message = 'La asociación del punto NFC se actualizó correctamente.';

        if ($request->expectsJson()) {
            $nfcPoint->load('news')->loadCount('scans');

            return response()->json([
                'message' => $message,
                'point' => $this->toAdminArray($nfcPoint, $this->recentPublishedNews(5)),
            ]);
        }

        $returnTo = $request->input('_return');

        if (is_string($returnTo) && str_starts_with($returnTo, url('/admin/nfc'))) {
            return redirect()
                ->to($returnTo)
                ->with('status', $message);
        }

        return redirect()
            ->route('admin.nfc.index')
            ->with('status', $message);
    }

    /**
     * @param  Collection<int, News>  $recentNews
     * @return array<string, mixed>
     */
    private function toAdminArray(NfcPoint $point, Collection $recentNews): array
    {
        $data = $point->toPublicArray();
        $data['news_id'] = $point->news_id;
        $data['news_title'] = $point->news?->title;
        $data['news_options'] = $this->newsOptionsForPoint($point, $recentNews)
            ->map(fn (News $item): array => [
                'id' => $item->id,
                'title' => $item->title,
            ])
            ->values()
            ->all();
        $data['urls'] = [
            'update' => route('admin.nfc.update', $point),
            'associate' => route('admin.nfc.associate', $point),
            'tour_edit' => route('admin.nfc.tour.edit', $point),
            'preview' => $point->hasTour()
                ? route('nfc.tour', $point->code)
                : route('nfc.show', $point->code),
        ];

        return $data;
    }

    /**
     * @return LengthAwarePaginator<int, News>
     */
    private function paginatedPublishedNewsForAssociation(NfcPoint $point, string $query, int $page): LengthAwarePaginator
    {
        return News::query()
            ->published()
            ->when($query !== '', function ($builder) use ($query): void {
                $builder->where(function ($inner) use ($query): void {
                    $inner
                        ->where('title', 'like', '%'.$query.'%')
                        ->orWhere('summary', 'like', '%'.$query.'%');
                });
            })
            ->orderByDesc('origin_published_at')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10, ['id', 'title', 'summary', 'origin_published_at', 'published_at'], 'page', $page);
    }

    /**
     * @param  LengthAwarePaginator<int, News>  $news
     * @return array<string, mixed>
     */
    private function associatePayload(NfcPoint $point, LengthAwarePaginator $news, string $query): array
    {
        return [
            'endpoint' => route('admin.nfc.associate', $point),
            'updateUrl' => route('admin.nfc.update', $point),
            'returnUrl' => route('admin.nfc.associate', $point),
            'query' => $query,
            'point' => [
                'code' => $point->code,
                'news_id' => $point->news_id,
                'news_title' => $point->news?->title,
            ],
            'data' => $news->getCollection()
                ->map(fn (News $item): array => $this->toAssociateNewsArray($point, $item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'from' => $news->firstItem(),
                'to' => $news->lastItem(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function toAssociateNewsArray(NfcPoint $point, News $item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'summary' => $item->summary,
            'published_on' => $item->origin_published_at?->toDateString()
                ?? $item->published_at?->toDateString(),
            'is_associated' => (int) $point->news_id === (int) $item->id,
        ];
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
