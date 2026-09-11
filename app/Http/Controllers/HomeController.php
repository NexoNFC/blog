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
        $published = News::query()
            ->published()
            ->with(['category', 'importedContent'])
            ->latest('published_at')
            ->get()
            ->map(fn (News $news): array => $news->toPublicArray());

        $contents = $published->all();
        $featured = $contents[0] ?? null;
        $rest = array_slice($contents, 1);

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
            'featured' => $featured,
            'contents' => $rest,
            'newsImages' => $images,
            'steps' => DemoCatalog::landingSteps(),
            'locations' => $locations,
        ]);
    }
}
