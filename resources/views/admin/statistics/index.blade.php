@extends('layouts.admin')

@section('title', 'Estadísticas')
@section('heading', 'Estadísticas')
@section('subtitle', 'Visitas a noticias y escaneos NFC')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2">
        <x-admin.stat-card label="Visitas a noticias" :value="$newsViewCount" />
        <x-admin.stat-card label="Escaneos NFC" :value="$nfcScanCount" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <x-ui.card>
            <h2 class="text-lg font-semibold">Noticias más visitadas</h2>
            @if ($mostVisited->isEmpty())
                <div class="mt-4">
                    <x-ui.empty-state title="Aún no hay visitas" description="Las consultas públicas a noticias publicadas aparecerán aquí." />
                </div>
            @else
                <ul class="mt-4 divide-y divide-white/40">
                    @foreach ($mostVisited as $item)
                        <li class="flex items-start justify-between gap-3 py-3">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-secondary">{{ $item->title }}</p>
                                <p class="text-xs text-secondary-light">{{ $item->category?->name ?? 'Sin categoría' }}</p>
                            </div>
                            <span class="shrink-0 text-sm text-secondary-light">{{ $item->views_count }} visitas</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold">Escaneos por punto NFC</h2>
            @if ($scansByPoint->isEmpty())
                <div class="mt-4">
                    <x-ui.empty-state title="Aún no hay puntos NFC" description="Cuando existan puntos en el campus verás aquí sus escaneos." />
                </div>
            @else
                <ul class="mt-4 divide-y divide-white/40">
                    @foreach ($scansByPoint as $point)
                        <li class="flex items-start justify-between gap-3 py-3">
                            <div class="min-w-0">
                                <p class="font-medium text-secondary">{{ $point->name }}</p>
                                <p class="text-xs text-secondary-light">
                                    {{ $point->identifier }}
                                    ·
                                    {{ $point->news?->title ?? 'Sin noticia asociada' }}
                                </p>
                            </div>
                            <span class="shrink-0 text-sm text-secondary-light">{{ $point->scans_count }} escaneos</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>
    </div>
@endsection
