<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoCatalog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $contents = DemoCatalog::contents();
        $points = DemoCatalog::nfcPoints();

        return view('admin.dashboard', [
            'contentCount' => count($contents),
            'publishedCount' => count(array_filter($contents, fn (array $item): bool => $item['status'] === 'publicado')),
            'nfcCount' => count($points),
            'activeNfcCount' => count(array_filter($points, fn (array $item): bool => $item['status'] === 'activo')),
            'recentContents' => array_slice($contents, 0, 3),
            'nfcPoints' => array_slice($points, 0, 4),
        ]);
    }
}
