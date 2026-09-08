<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsView;
use App\Models\NfcPoint;
use App\Models\NfcScan;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $scansByPoint = NfcPoint::query()
            ->with('news:id,title,slug')
            ->withCount('scans')
            ->orderBy('identifier')
            ->get();

        $mostVisited = News::query()
            ->with('category')
            ->withCount('views')
            ->whereHas('views')
            ->orderByDesc('views_count')
            ->limit(8)
            ->get();

        return view('admin.statistics.index', [
            'newsViewCount' => NewsView::query()->count(),
            'nfcScanCount' => NfcScan::query()->count(),
            'scansByPoint' => $scansByPoint,
            'mostVisited' => $mostVisited,
        ]);
    }
}
