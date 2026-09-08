<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\News;
use App\Models\NfcPoint;
use App\Services\VisitRecorder;
use Illuminate\View\View;

class NfcPointController extends Controller
{
    public function __construct(private VisitRecorder $visits) {}

    public function show(string $code): View
    {
        $point = NfcPoint::query()
            ->with(['news.category'])
            ->where('code', $code)
            ->firstOrFail();

        if ($point->isActive()) {
            $this->visits->recordNfcScan($point);
        }

        $news = $point->news;
        $contents = ($news !== null && $news->status === ContentStatus::Published)
            ? [$news->toPublicArray()]
            : [];

        $moreNews = News::query()
            ->published()
            ->with('category')
            ->when($news !== null, fn ($query) => $query->whereKeyNot($news->id))
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(fn (News $item): array => $item->toPublicArray())
            ->all();

        return view('nfc.show', [
            'point' => $point->toPublicArray(),
            'contents' => $contents,
            'moreNews' => $moreNews,
        ]);
    }
}
