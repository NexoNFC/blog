@extends('layouts.admin')

@section('title', 'Puntos NFC')
@section('heading', 'Puntos NFC')
@section('subtitle', 'Administración conceptual de puntos físicos')

@section('content')
    <x-ui.alert type="info" class="mb-6">
        El código del punto es estable. El contenido asociado se cambia desde la plataforma sin reprogramar el chip.
    </x-ui.alert>

    <div class="overflow-hidden">
    <x-ui.table>
        <thead class="border-b border-white/40 bg-white/30 text-xs uppercase tracking-wide text-secondary-light">
            <tr>
                <th class="px-4 py-3 font-semibold">Identificador</th>
                <th class="px-4 py-3 font-semibold">Nombre</th>
                <th class="px-4 py-3 font-semibold">Ubicación</th>
                <th class="px-4 py-3 font-semibold">Estado</th>
                <th class="px-4 py-3 font-semibold">Contenidos</th>
                <th class="px-4 py-3 font-semibold">Escaneos</th>
                <th class="px-4 py-3 font-semibold"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/40">
            @foreach ($points as $point)
                <tr class="transition hover:bg-white/35">
                    <td class="px-4 py-3 font-medium text-secondary">{{ $point['identifier'] }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $point['name'] }}</p>
                        <p class="text-xs text-secondary-light">/nfc/{{ $point['code'] }}</p>
                    </td>
                    <td class="px-4 py-3 text-secondary-light">{{ $point['location'] }}</td>
                    <td class="px-4 py-3">
                        <x-nfc.point-status :status="$point['status']" />
                    </td>
                    <td class="px-4 py-3 text-secondary-light">{{ count($point['content_slugs']) }}</td>
                    <td class="px-4 py-3 text-secondary-light">{{ $point['scans_demo'] }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('nfc.show', $point['code']) }}" class="font-semibold text-primary hover:underline">Probar</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
    </div>
@endsection
