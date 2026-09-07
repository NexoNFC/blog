@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subtitle', 'Resumen operativo de la maqueta')

@section('content')
    <x-ui.alert type="info" class="mb-6">
        Maqueta con datos de ejemplo. Autenticación y persistencia llegarán en tareas posteriores. Esta plataforma complementa el
        <a href="https://www.fesc.edu.co/portal/" target="_blank" rel="noopener noreferrer" class="font-semibold underline underline-offset-2">sitio oficial FESC</a>.
    </x-ui.alert>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Contenidos" :value="$contentCount" />
        <x-admin.stat-card label="Publicados" :value="$publishedCount" />
        <x-admin.stat-card label="Puntos NFC" :value="$nfcCount" />
        <x-admin.stat-card label="NFC activos" :value="$activeNfcCount" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="rounded border border-muted bg-surface p-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">Contenidos recientes</h2>
                <x-ui.button href="{{ route('admin.contents.index') }}" variant="ghost" class="!px-2 !py-1">Ver todos</x-ui.button>
            </div>
            <ul class="mt-4 divide-y divide-muted">
                @foreach ($recentContents as $content)
                    <li class="flex items-start justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-secondary">{{ $content['title'] }}</p>
                            <p class="text-xs text-secondary-light">{{ $content['type'] }}</p>
                        </div>
                        <x-ui.badge :tone="$content['status'] === 'publicado' ? 'success' : 'warning'">{{ $content['status'] }}</x-ui.badge>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="rounded border border-muted bg-surface p-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">Puntos NFC</h2>
                <x-ui.button href="{{ route('admin.nfc.index') }}" variant="ghost" class="!px-2 !py-1">Gestionar</x-ui.button>
            </div>
            <ul class="mt-4 divide-y divide-muted">
                @foreach ($nfcPoints as $point)
                    <li class="flex items-start justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <p class="font-medium text-secondary">{{ $point['name'] }}</p>
                            <p class="text-xs text-secondary-light">{{ $point['identifier'] }} · {{ $point['scans_demo'] }} escaneos (demo)</p>
                        </div>
                        <x-nfc.point-status :status="$point['status']" />
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
@endsection
