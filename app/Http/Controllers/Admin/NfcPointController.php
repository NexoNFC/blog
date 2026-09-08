<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateNfcPointAssociationRequest;
use App\Models\News;
use App\Models\NfcPoint;
use App\Services\NfcAssociationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class NfcPointController extends Controller
{
    public function __construct(private NfcAssociationService $associations) {}

    public function index(): View
    {
        $this->authorize('viewAny', NfcPoint::class);

        $points = NfcPoint::query()
            ->with('news')
            ->withCount('scans')
            ->orderBy('identifier')
            ->get()
            ->map(function (NfcPoint $point): array {
                $data = $point->toPublicArray();
                $data['news_id'] = $point->news_id;
                $data['news_title'] = $point->news?->title;

                return $data;
            })
            ->all();

        return view('admin.nfc-points.index', [
            'points' => $points,
            'publishedNews' => News::query()
                ->where('status', ContentStatus::Published)
                ->orderBy('title')
                ->get(['id', 'title', 'slug']),
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

        return redirect()
            ->route('admin.nfc.index')
            ->with('status', 'La asociación del punto NFC se actualizó correctamente.');
    }
}
