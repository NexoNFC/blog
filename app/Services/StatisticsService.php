<?php

namespace App\Services;

use App\Enums\NfcPointStatus;
use App\Models\News;
use App\Models\NewsView;
use App\Models\NfcPoint;
use App\Models\NfcScan;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    private const TREND_DAYS = 14;

    /**
     * @return array{
     *     kpis: list<array{label: string, value: int|string, hint: string}>,
     *     trend: array{labels: list<string>, views: list<int>, scans: list<int>},
     *     scansByPoint: array{labels: list<string>, values: list<int>},
     *     topNews: array{labels: list<string>, values: list<int>},
     *     mostVisited: Collection<int, News>,
     *     scansByPointList: Collection<int, NfcPoint>
     * }
     */
    public function dashboard(): array
    {
        $from = now()->subDays(self::TREND_DAYS - 1)->startOfDay();

        $newsViewCount = NewsView::query()->count();
        $nfcScanCount = NfcScan::query()->count();
        $viewsLast7Days = NewsView::query()->where('viewed_at', '>=', now()->subDays(6)->startOfDay())->count();
        $scansLast7Days = NfcScan::query()->where('scanned_at', '>=', now()->subDays(6)->startOfDay())->count();
        $activeNfcCount = NfcPoint::query()->where('status', NfcPointStatus::Active)->count();
        $pointsWithScans = NfcPoint::query()->whereHas('scans')->count();

        $mostVisited = News::query()
            ->with('category')
            ->withCount('views')
            ->whereHas('views')
            ->orderByDesc('views_count')
            ->limit(8)
            ->get();

        $scansByPointList = NfcPoint::query()
            ->with('news:id,title,slug')
            ->withCount('scans')
            ->orderByDesc('scans_count')
            ->orderBy('identifier')
            ->get();

        $scansForChart = $scansByPointList->filter(fn (NfcPoint $point) => $point->scans_count > 0);

        return [
            'kpis' => [
                [
                    'label' => 'Visitas a noticias',
                    'value' => $newsViewCount,
                    'hint' => 'Total acumulado',
                ],
                [
                    'label' => 'Escaneos NFC',
                    'value' => $nfcScanCount,
                    'hint' => 'Total acumulado',
                ],
                [
                    'label' => 'Visitas (7 días)',
                    'value' => $viewsLast7Days,
                    'hint' => 'Consultas de contenido',
                ],
                [
                    'label' => 'Escaneos (7 días)',
                    'value' => $scansLast7Days,
                    'hint' => 'Accesos por chip',
                ],
                [
                    'label' => 'Puntos NFC activos',
                    'value' => $activeNfcCount,
                    'hint' => 'Disponibles en campus',
                ],
                [
                    'label' => 'Puntos con actividad',
                    'value' => $pointsWithScans,
                    'hint' => 'Al menos un escaneo',
                ],
            ],
            'trend' => $this->dailyTrend($from),
            'scansByPoint' => [
                'labels' => $scansForChart->pluck('name')->values()->all(),
                'values' => $scansForChart->pluck('scans_count')->map(fn ($count) => (int) $count)->values()->all(),
            ],
            'topNews' => [
                'labels' => $mostVisited->pluck('title')->map(fn (string $title) => $this->truncate($title, 36))->values()->all(),
                'values' => $mostVisited->pluck('views_count')->map(fn ($count) => (int) $count)->values()->all(),
            ],
            'mostVisited' => $mostVisited,
            'scansByPointList' => $scansByPointList,
        ];
    }

    /**
     * @return array{labels: list<string>, views: list<int>, scans: list<int>}
     */
    private function dailyTrend(Carbon $from): array
    {
        $viewCounts = $this->countsByDay(NewsView::query()->where('viewed_at', '>=', $from), 'viewed_at');
        $scanCounts = $this->countsByDay(NfcScan::query()->where('scanned_at', '>=', $from), 'scanned_at');

        $labels = [];
        $views = [];
        $scans = [];

        foreach (CarbonPeriod::create($from, now()->endOfDay()) as $day) {
            $key = $day->toDateString();
            $labels[] = $day->translatedFormat('d M');
            $views[] = (int) ($viewCounts[$key] ?? 0);
            $scans[] = (int) ($scanCounts[$key] ?? 0);
        }

        return compact('labels', 'views', 'scans');
    }

    /**
     * @param  Builder<Model>  $query
     * @return array<string, int>
     */
    private function countsByDay($query, string $column): array
    {
        $driver = DB::connection()->getDriverName();
        $expression = match ($driver) {
            'sqlite' => "strftime('%Y-%m-%d', {$column})",
            default => "DATE({$column})",
        };

        return $query
            ->selectRaw("{$expression} as day, COUNT(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    private function truncate(string $value, int $limit): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit - 1)).'…';
    }
}
