@extends('layouts.admin')

@section('title', 'Estadísticas')
@section('heading', 'Estadísticas')
@section('subtitle', 'KPIs y tendencias de visitas y escaneos NFC')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($kpis as $kpi)
            <x-admin.stat-card
                :label="$kpi['label']"
                :value="$kpi['value']"
                :hint="$kpi['hint']"
            />
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <x-ui.card>
            <h2 class="text-lg font-semibold text-secondary">Actividad últimos 14 días</h2>
            <p class="mt-1 text-sm text-secondary-light">Visitas a noticias y escaneos NFC por día</p>
            <div class="mt-4 h-72">
                <canvas
                    id="stats-trend-chart"
                    aria-label="Gráfica de actividad diaria"
                    data-labels='@json($trend['labels'])'
                    data-views='@json($trend['views'])'
                    data-scans='@json($trend['scans'])'
                ></canvas>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold text-secondary">Escaneos por punto NFC</h2>
            <p class="mt-1 text-sm text-secondary-light">Distribución acumulada por ubicación</p>
            @if (collect($scansByPoint['values'])->sum() === 0)
                <div class="mt-4">
                    <x-ui.empty-state title="Aún no hay escaneos" description="Cuando se usen puntos NFC del campus verás la gráfica aquí." />
                </div>
            @else
                @php
                    $scansPointCount = max(count($scansByPoint['labels']), 1);
                    $scansChartHeight = max(288, $scansPointCount * 44 + 48);
                @endphp
                <div class="mt-4" style="height: {{ $scansChartHeight }}px">
                    <canvas
                        id="stats-scans-chart"
                        aria-label="Gráfica de escaneos por punto"
                        data-labels='@json($scansByPoint['labels'])'
                        data-values='@json($scansByPoint['values'])'
                    ></canvas>
                </div>
            @endif
        </x-ui.card>

        <x-ui.card class="xl:col-span-2">
            <h2 class="text-lg font-semibold text-secondary">Noticias más visitadas</h2>
            <p class="mt-1 text-sm text-secondary-light">Top de consultas públicas al catálogo</p>
            @if ($mostVisited->isEmpty())
                <div class="mt-4">
                    <x-ui.empty-state title="Aún no hay visitas" description="Las consultas públicas a noticias publicadas aparecerán aquí." />
                </div>
            @else
                <div class="mt-4 grid gap-6 lg:grid-cols-2">
                    <div class="h-72">
                        <canvas
                            id="stats-news-chart"
                            aria-label="Gráfica de noticias más visitadas"
                            data-labels='@json($topNews['labels'])'
                            data-values='@json($topNews['values'])'
                        ></canvas>
                    </div>
                    <x-ui.table inset>
                        <thead>
                            <tr>
                                <th scope="col">Noticia</th>
                                <th scope="col">Categoría</th>
                                <th scope="col">Visitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mostVisited as $item)
                                <tr>
                                    <th scope="row">{{ $item->title }}</th>
                                    <td class="text-secondary-light">{{ $item->category?->name ?? 'Sin categoría' }}</td>
                                    <td class="whitespace-nowrap text-secondary-light">{{ $item->views_count }} visitas</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-ui.table>
                </div>
            @endif
        </x-ui.card>
    </div>

    <div class="mt-8">
        <x-ui.table>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-secondary">Detalle por punto NFC</h2>
            </x-slot:header>
            <thead>
                <tr>
                    <th scope="col">Punto</th>
                    <th scope="col">Noticia asociada</th>
                    <th scope="col">Escaneos</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($scansByPointList as $point)
                    <tr>
                        <th scope="row">
                            <p>{{ $point->name }}</p>
                            <p class="text-xs font-normal text-secondary-light">{{ $point->identifier }}</p>
                        </th>
                        <td class="text-secondary-light">{{ $point->news?->title ?? 'Sin noticia asociada' }}</td>
                        <td class="whitespace-nowrap text-secondary-light">{{ $point->scans_count }} escaneos</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <x-ui.empty-state embedded title="Aún no hay puntos NFC" description="Cuando existan puntos en el campus verás aquí sus escaneos." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js" defer></script>
    <script defer>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') {
                return;
            }

            const brand = {
                primary: '#c8102e',
                primarySoft: 'rgba(200, 16, 46, 0.18)',
                secondary: '#434345',
                secondarySoft: 'rgba(67, 67, 69, 0.16)',
                muted: '#6b6b6e',
                grid: 'rgba(67, 67, 69, 0.08)',
            };

            const readJson = (el, key) => {
                try {
                    return JSON.parse(el.getAttribute(key) || '[]');
                } catch {
                    return [];
                }
            };

            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: brand.secondary,
                            boxWidth: 12,
                            usePointStyle: true,
                        },
                    },
                    tooltip: {
                        backgroundColor: '#1a1a1b',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                    },
                },
                scales: {
                    x: {
                        ticks: { color: brand.muted, maxRotation: 45, minRotation: 0 },
                        grid: { color: brand.grid },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: brand.muted, precision: 0 },
                        grid: { color: brand.grid },
                    },
                },
            };

            const trend = document.getElementById('stats-trend-chart');
            if (trend) {
                new Chart(trend, {
                    type: 'line',
                    data: {
                        labels: readJson(trend, 'data-labels'),
                        datasets: [
                            {
                                label: 'Visitas',
                                data: readJson(trend, 'data-views'),
                                borderColor: brand.primary,
                                backgroundColor: brand.primarySoft,
                                fill: true,
                                tension: 0.35,
                                pointRadius: 3,
                                pointBackgroundColor: brand.primary,
                            },
                            {
                                label: 'Escaneos NFC',
                                data: readJson(trend, 'data-scans'),
                                borderColor: brand.secondary,
                                backgroundColor: brand.secondarySoft,
                                fill: true,
                                tension: 0.35,
                                pointRadius: 3,
                                pointBackgroundColor: brand.secondary,
                            },
                        ],
                    },
                    options: baseOptions,
                });
            }

            const scans = document.getElementById('stats-scans-chart');
            if (scans) {
                const scanLabels = readJson(scans, 'data-labels');

                new Chart(scans, {
                    type: 'bar',
                    data: {
                        labels: scanLabels,
                        datasets: [{
                            label: 'Escaneos',
                            data: readJson(scans, 'data-values'),
                            backgroundColor: brand.primary,
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 28,
                            categoryPercentage: 0.7,
                            barPercentage: 0.85,
                        }],
                    },
                    options: {
                        ...baseOptions,
                        indexAxis: 'y',
                        layout: {
                            padding: { top: 4, right: 12, bottom: 4, left: 4 },
                        },
                        plugins: {
                            ...baseOptions.plugins,
                            legend: { display: false },
                            tooltip: {
                                ...baseOptions.plugins.tooltip,
                                callbacks: {
                                    title: (items) => items[0]?.label ?? '',
                                    label: (item) => ` ${item.formattedValue} escaneos`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { color: brand.muted, precision: 0 },
                                grid: { color: brand.grid },
                                border: { display: false },
                            },
                            y: {
                                ticks: {
                                    color: brand.secondary,
                                    crossAlign: 'far',
                                    autoSkip: false,
                                    callback(value) {
                                        const label = this.getLabelForValue(value);
                                        if (typeof label !== 'string') {
                                            return label;
                                        }

                                        return label.length > 28 ? `${label.slice(0, 27)}…` : label;
                                    },
                                },
                                grid: { display: false },
                                border: { display: false },
                            },
                        },
                    },
                });
            }

            const news = document.getElementById('stats-news-chart');
            if (news) {
                new Chart(news, {
                    type: 'doughnut',
                    data: {
                        labels: readJson(news, 'data-labels'),
                        datasets: [{
                            data: readJson(news, 'data-values'),
                            backgroundColor: [
                                '#c8102e',
                                '#9e0c24',
                                '#e85a6f',
                                '#434345',
                                '#6b6b6e',
                                '#9a9a9d',
                                '#f8e8eb',
                                '#1e3a5f',
                            ],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: brand.secondary,
                                    boxWidth: 12,
                                    usePointStyle: true,
                                },
                            },
                        },
                    },
                });
            }
        });
    </script>
@endpush
