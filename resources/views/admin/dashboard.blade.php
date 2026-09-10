@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subtitle', 'Resumen operativo de noticias y puntos NFC')

@section('content')
    <x-ui.alert type="info" class="mb-6">
        Panel administrativo protegido. Esta plataforma complementa el
        <a href="https://www.fesc.edu.co/portal/" target="_blank" rel="noopener noreferrer" class="font-semibold underline underline-offset-2">sitio oficial FESC</a>.
    </x-ui.alert>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Contenidos" :value="$contentCount" />
        <x-admin.stat-card label="Publicados" :value="$publishedCount" />
        <x-admin.stat-card label="Puntos NFC" :value="$nfcCount" />
        <x-admin.stat-card label="NFC activos" :value="$activeNfcCount" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <x-ui.table>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-secondary">Contenidos recientes</h2>
                @can('news.view')
                    <x-ui.button href="{{ route('admin.news.index') }}" variant="ghost" size="sm">Ver todos</x-ui.button>
                @endcan
            </x-slot:header>
            <thead>
                <tr>
                    <th scope="col">Título</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentContents as $content)
                    <tr>
                        <th scope="row">{{ $content['title'] }}</th>
                        <td class="text-secondary-light">{{ $content['type'] }}</td>
                        <td>
                            <x-ui.badge :tone="$content['status'] === 'publicado' ? 'success' : 'warning'">{{ $content['status'] }}</x-ui.badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <x-ui.empty-state embedded title="Sin contenidos" description="Cuando haya noticias en el catálogo aparecerán aquí." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>

        <x-ui.table>
            <x-slot:header>
                <h2 class="text-lg font-semibold text-secondary">Puntos NFC</h2>
                <x-ui.button href="{{ route('admin.nfc.index') }}" variant="ghost" size="sm">Gestionar</x-ui.button>
            </x-slot:header>
            <thead>
                <tr>
                    <th scope="col">Punto</th>
                    <th scope="col">Identificador</th>
                    <th scope="col">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($nfcPoints as $point)
                    <tr>
                        <th scope="row">
                            <p>{{ $point['name'] }}</p>
                            <p class="text-xs font-normal text-secondary-light">{{ $point['scans_count'] }} escaneos</p>
                        </th>
                        <td class="whitespace-nowrap text-secondary-light">{{ $point['identifier'] }}</td>
                        <td>
                            <x-nfc.point-status :status="$point['status']" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <x-ui.empty-state embedded title="Sin puntos NFC" description="Cuando existan puntos en el campus aparecerán aquí." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </div>
@endsection
