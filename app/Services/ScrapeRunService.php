<?php

namespace App\Services;

use App\Enums\ScrapeRunStatus;
use App\Models\ScrapeRun;

class ScrapeRunService
{
    public function start(string $trigger = 'scheduled'): ScrapeRun
    {
        return ScrapeRun::query()->create([
            'status' => ScrapeRunStatus::Running,
            'trigger' => $trigger,
            'started_at' => now(),
        ]);
    }

    /**
     * @param  array{
     *     contents_found?: int,
     *     contents_new?: int,
     *     contents_processed?: int,
     *     news_created?: int
     * }  $stats
     */
    public function markCompleted(ScrapeRun $run, array $stats = []): ScrapeRun
    {
        $run->fill([
            'status' => ScrapeRunStatus::Completed,
            'finished_at' => now(),
            'contents_found' => $stats['contents_found'] ?? 0,
            'contents_new' => $stats['contents_new'] ?? 0,
            'contents_processed' => $stats['contents_processed'] ?? 0,
            'news_created' => $stats['news_created'] ?? 0,
            'error_message' => null,
        ]);
        $run->save();

        return $run;
    }

    public function markFailed(ScrapeRun $run, string $message): ScrapeRun
    {
        $run->fill([
            'status' => ScrapeRunStatus::Error,
            'finished_at' => now(),
            'error_message' => $message,
        ]);
        $run->save();

        return $run;
    }
}
