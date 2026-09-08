<?php

namespace App\Services;

use App\Models\News;
use App\Models\NewsView;
use App\Models\NfcPoint;
use App\Models\NfcScan;

class VisitRecorder
{
    public function recordNewsView(News $news): NewsView
    {
        return NewsView::query()->create([
            'news_id' => $news->id,
            'viewed_at' => now(),
        ]);
    }

    public function recordNfcScan(NfcPoint $point): NfcScan
    {
        return NfcScan::query()->create([
            'nfc_point_id' => $point->id,
            'news_id' => $point->news_id,
            'scanned_at' => now(),
        ]);
    }
}
