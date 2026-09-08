@extends('layouts.admin')

@section('title', 'Puntos NFC')
@section('heading', 'Puntos NFC')
@section('subtitle', 'Un punto físico, una noticia activa')

@section('content')
    <x-ui.alert type="info" class="mb-6">
        El código del punto es estable. La noticia asociada se cambia desde la plataforma sin reprogramar el chip.
    </x-ui.alert>

    <x-ui.table>
        <thead class="border-b border-white/40 bg-white/30 text-xs uppercase tracking-wide text-secondary-light">
            <tr>
                <th class="px-4 py-3 font-semibold">Identificador</th>
                <th class="px-4 py-3 font-semibold">Nombre</th>
                <th class="px-4 py-3 font-semibold">Ubicación</th>
                <th class="px-4 py-3 font-semibold">Estado</th>
                <th class="px-4 py-3 font-semibold">Noticia activa</th>
                <th class="px-4 py-3 font-semibold">Escaneos</th>
                <th class="px-4 py-3 font-semibold"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/40">
            @forelse ($points as $point)
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
                    <td class="px-4 py-3">
                        @can('nfc.manage-content')
                            <form method="POST" action="{{ route('admin.nfc.update', $point['code']) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                @csrf
                                @method('PATCH')
                                <label class="sr-only" for="news_id_{{ $point['code'] }}">Noticia para {{ $point['name'] }}</label>
                                <x-form.select id="news_id_{{ $point['code'] }}" name="news_id" class="min-w-48">
                                    <option value="">Sin noticia</option>
                                    @foreach ($publishedNews as $item)
                                        <option value="{{ $item->id }}" @selected((int) $point['news_id'] === (int) $item->id)>
                                            {{ $item->title }}
                                        </option>
                                    @endforeach
                                </x-form.select>
                                <x-ui.button type="submit" size="sm" variant="secondary">Asociar</x-ui.button>
                            </form>
                        @else
                            <p class="text-sm text-secondary-light">{{ $point['news_title'] ?? 'Sin noticia' }}</p>
                        @endcan
                    </td>
                    <td class="px-4 py-3 text-secondary-light">{{ $point['scans_count'] }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('nfc.show', $point['code']) }}" class="font-semibold text-primary hover:underline">Probar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8">
                        <x-ui.empty-state title="Sin puntos NFC" description="Cuando existan puntos en el campus aparecerán aquí para asociarles una noticia." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>
@endsection
