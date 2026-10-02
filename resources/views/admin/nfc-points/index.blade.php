@extends('layouts.admin')

@section('title', 'Puntos NFC')
@section('heading', 'Puntos NFC')
@section('subtitle', 'Un punto físico, una noticia activa')

@section('content')
    <x-ui.alert type="info" class="mb-6">
        El código del punto es estable. La noticia asociada se cambia desde la plataforma sin reprogramar el chip.
        El selector muestra las cinco más recientes; para el resto usa «Buscar en todas las noticias…».
    </x-ui.alert>

    <x-ui.table>
        <thead>
            <tr>
                <th scope="col" class="min-w-52">Punto</th>
                <th scope="col">Estado</th>
                <th scope="col" class="min-w-[22rem]">Noticia activa</th>
                <th scope="col">Escaneos</th>
                <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($points as $point)
                <tr>
                    <th scope="row">
                        <p>{{ $point['name'] }}</p>
                        <p class="mt-0.5 text-xs font-normal text-secondary-light">
                            {{ $point['identifier'] }}
                            @if ($point['location'] !== $point['name'])
                                · {{ $point['location'] }}
                            @endif
                            · /nfc/{{ $point['code'] }}
                        </p>
                    </th>
                    <td>
                        <x-nfc.point-status :status="$point['status']" />
                    </td>
                    <td>
                        <x-nfc.associate-form :point="$point" />
                    </td>
                    <td class="whitespace-nowrap text-secondary-light">{{ $point['scans_count'] }}</td>
                    <td class="whitespace-nowrap text-right">
                        <div class="inline-flex w-[9.75rem] flex-col gap-1.5">
                            @can('nfc.manage-content')
                                <x-ui.button
                                    href="{{ route('admin.nfc.associate', $point['code']) }}"
                                    size="sm"
                                    variant="secondary"
                                    class="w-full"
                                >
                                    Elegir noticia
                                </x-ui.button>
                            @endcan
                            <x-ui.button
                                href="{{ route('nfc.show', $point['code']) }}"
                                size="sm"
                                variant="secondary"
                                class="w-full"
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
@endsection
