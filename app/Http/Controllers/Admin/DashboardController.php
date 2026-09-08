<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Enums\NfcPointStatus;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NfcPoint;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $contents = News::query()
            ->with('category')
            ->latest()
            ->get();

        $points = NfcPoint::query()
            ->withCount('scans')
            ->orderBy('identifier')
            ->get();

        return view('admin.dashboard', [
            'contentCount' => $contents->count(),
            'publishedCount' => $contents->where('status', ContentStatus::Published)->count(),
            'nfcCount' => $points->count(),
            'activeNfcCount' => $points->where('status', NfcPointStatus::Active)->count(),
            'recentContents' => $contents->take(3)->map(fn (News $news): array => $news->toPublicArray())->all(),
            'nfcPoints' => $points->take(4)->map(fn (NfcPoint $point): array => $point->toPublicArray())->all(),
        ]);
    }
}
