@extends('layouts.admin')

@section('title', 'Contenidos')
@section('heading', 'Contenidos')
@section('subtitle', 'Listado editorial de ejemplo')

@section('actions')
    @can('news.create')
        <x-ui.button href="{{ route('admin.news.create') }}">Nueva noticia</x-ui.button>
    @endcan
@endsection

@section('content')
    <x-ui.table>
        <thead class="border-b border-white/40 bg-white/30 text-xs uppercase tracking-wide text-secondary-light">
            <tr>
                <th class="px-4 py-3 font-semibold">Título</th>
                <th class="px-4 py-3 font-semibold">Tipo</th>
                <th class="px-4 py-3 font-semibold">Estado</th>
                <th class="px-4 py-3 font-semibold">Publicación</th>
                <th class="px-4 py-3 font-semibold"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/40">
            @foreach ($contents as $content)
                <tr class="transition hover:bg-white/35">
                    <td class="px-4 py-3">
                        <p class="font-medium text-secondary">{{ $content['title'] }}</p>
                        <p class="mt-0.5 max-w-md truncate text-xs text-secondary-light">{{ $content['summary'] }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge>{{ $content['type'] }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge :tone="$content['status'] === 'publicado' ? 'success' : 'warning'">{{ $content['status'] }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-secondary-light">
                        {{ $content['published_at'] ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            @if ($content['status'] === 'publicado')
                                <a href="{{ route('contents.show', $content['slug']) }}" class="font-semibold text-primary hover:underline">Ver</a>
                            @else
                                <span class="text-secondary-light">Borrador</span>
                            @endif

                            @can('news.delete')
                                <form method="POST" action="{{ route('admin.news.destroy', $content['slug']) }}" onsubmit="return confirm('¿Eliminar esta noticia?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-danger hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
@endsection
