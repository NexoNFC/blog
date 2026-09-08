@extends('layouts.admin')

@section('title', 'Contenidos')
@section('heading', 'Contenidos')
@section('subtitle', 'Borradores, publicados e histórico')

@section('content')
    <x-ui.alert type="info" class="mb-6">
        Las noticias llegan desde fuentes oficiales. Aquí se revisan, editan y publican; no se crean desde cero como flujo principal.
    </x-ui.alert>

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
            @forelse ($contents as $content)
                <tr class="transition hover:bg-white/35">
                    <td class="px-4 py-3">
                        <p class="font-medium text-secondary">{{ $content['title'] }}</p>
                        <p class="mt-0.5 max-w-md truncate text-xs text-secondary-light">{{ $content['summary'] }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge>{{ $content['type'] }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge :tone="$content['status'] === 'publicado' ? 'success' : ($content['status'] === 'archivado' ? 'neutral' : 'warning')">{{ $content['status'] }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-secondary-light">
                        {{ $content['published_at'] ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex flex-wrap items-center justify-end gap-3">
                            @can('news.update')
                                <a href="{{ route('admin.news.edit', $content['slug']) }}" class="font-semibold text-primary hover:underline">Editar</a>
                            @endcan

                            @if ($content['status'] === 'publicado')
                                <a href="{{ route('contents.show', $content['slug']) }}" class="font-semibold text-primary hover:underline">Ver</a>
                            @elseif ($content['status'] === 'borrador')
                                <form method="POST" action="{{ route('admin.news.publish', $content['slug']) }}">
                                    @csrf
                                    @can('news.publish')
                                        <button type="submit" class="font-semibold text-primary hover:underline">Publicar</button>
                                    @endcan
                                </form>
                            @endif

                            @if ($content['status'] !== 'archivado')
                                @can('news.delete')
                                    <form method="POST" action="{{ route('admin.news.destroy', $content['slug']) }}" onsubmit="return confirm('¿Archivar esta noticia? Permanecerá en el histórico.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-danger hover:underline">Archivar</button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8">
                        <x-ui.empty-state title="Sin noticias" description="Cuando se ejecute la extracción aparecerán aquí como borradores." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>
@endsection
