<?php

namespace App\Http\Controllers;

use App\Enums\NfcPointStatus;
use App\Models\News;
use App\Models\NfcPoint;
use App\Support\DemoCatalog;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $contents = News::query()
            ->published()
            ->whereNotNull('imported_content_id')
            ->with(['category', 'importedContent'])
            ->orderByDesc('origin_published_at')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(fn (News $news): array => $news->toPublicArray())
            ->all();

        $images = [];
        foreach ($contents as $content) {
            if (! empty($content['image'])) {
                $images[$content['slug']] = $content['image'];
            }
        }

        $locations = NfcPoint::query()
            ->where('status', NfcPointStatus::Active)
            ->orderBy('identifier')
            ->get()
            ->map(fn (NfcPoint $point): array => $point->toCampusLocationArray())
            ->all();

        return view('home', [
            'contents' => $contents,
            'newsImages' => $images,
            'steps' => DemoCatalog::landingSteps(),
            'locations' => $locations,
        ]);
    }
}
