@extends('layouts.admin')

@section('title', 'Puntos NFC')
@section('heading', 'Puntos NFC')
@section('subtitle', 'Un punto físico, una noticia activa')

@php
    $nfcIndexConfig = [
        'points' => $points,
        'canManage' => auth()->user()?->can('nfc.manage-content') ?? false,
    ];
@endphp

@section('content')
    <div
        x-data="adminNfcIndex({{ \Illuminate\Support\Js::from($nfcIndexConfig) }})"
        class="space-y-6"
    >
        <x-ui.alert type="info" class="mb-0">
            El código del punto es estable. La noticia asociada se cambia desde la plataforma sin reprogramar el chip.
            El selector muestra las cinco más recientes; para el resto usa «Buscar en todas las noticias…».
        </x-ui.alert>

        <x-ui.table table-class="data-table--nfc" wide>
            <thead>
                <tr>
                    <th scope="col" class="data-table__col-point">Punto</th>
                    <th scope="col" class="data-table__col-status">Estado</th>
                    <th scope="col" class="data-table__col-news">Noticia activa</th>
                    <th scope="col" class="data-table__col-scans">Escaneos</th>
                    <th scope="col" class="data-table__col-actions">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($points as $point)
                    <tr>
                        <th scope="row" class="data-table__col-point">
                            <div class="nfc-point-cell">
                                <p class="nfc-point-cell__name">{{ $point['name'] }}</p>
                                <p class="nfc-point-cell__meta">
                                    <span>{{ $point['identifier'] }}</span>
                                    <span class="nfc-point-cell__sep" aria-hidden="true">·</span>
                                    <span>/nfc/{{ $point['code'] }}</span>
                                </p>
                                @if (! empty($point['location']) && $point['location'] !== $point['name'])
                                    <p class="nfc-point-cell__location">{{ $point['location'] }}</p>
                                @endif
                            </div>
                        </th>
                        <td class="data-table__col-status">
                            <x-nfc.point-status :status="$point['status']" />
                        </td>
                        <td class="data-table__col-news">
                            <x-nfc.associate-form :point="$point" />
                        </td>
                        <td class="data-table__col-scans">
                            <span class="nfc-scans-cell">{{ $point['scans_count'] }}</span>
                        </td>
                        <td class="data-table__col-actions">
                            <div class="nfc-actions-cell">
                                @can('nfc.manage-content')
                                    <x-ui.button
                                        href="{{ route('admin.nfc.associate', $point['code']) }}"
                                        size="sm"
                                        variant="secondary"
                                    >
                                        Noticia
                                    </x-ui.button>
                                    <x-ui.button
                                        href="{{ route('admin.nfc.tour.edit', $point['code']) }}"
                                        size="sm"
                                        variant="secondary"
                                    >
                                        360°
                                    </x-ui.button>
                                @endcan
                                <x-ui.button
                                    href="{{ $point['urls']['preview'] ?? ((! empty($point['has_tour']) ? route('nfc.tour', $point['code']) : route('nfc.show', $point['code']))) }}"
                                    size="sm"
                                    variant="secondary"
                                >
                                    Probar
                                </x-ui.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-ui.empty-state embedded title="Sin puntos NFC" description="Cuando existan puntos en el campus aparecerán aquí para asociarles una noticia." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </div>
@endsection
