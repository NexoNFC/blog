@extends('layouts.admin')

@section('title', 'Contenidos')
@section('heading', 'Contenidos')
@section('subtitle', 'Borradores, publicados e histórico')

@section('content')
    <x-ui.alert type="info" class="mb-6">
        Las noticias llegan desde fuentes oficiales. Aquí se revisan, editan y publican; no se crean desde cero como flujo principal.
    </x-ui.alert>

    <x-ui.table wide>
        <thead>
            <tr>
                <th scope="col">Título</th>
                <th scope="col">Tipo</th>
                <th scope="col">Estado</th>
                <th scope="col">Publicación</th>
                <th scope="col" class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contents as $content)
                <tr>
                    <th scope="row" class="min-w-[16rem] max-w-sm">
                        <p class="leading-snug">{{ $content['title'] }}</p>
                        <p class="mt-0.5 truncate text-xs font-normal text-secondary-light">{{ $content['summary'] }}</p>
                    </th>
                    <td class="whitespace-nowrap">
                        <x-ui.badge>{{ $content['type'] }}</x-ui.badge>
                    </td>
                    <td class="whitespace-nowrap">
                        <x-ui.badge :tone="$content['status'] === 'publicado' ? 'success' : ($content['status'] === 'archivado' ? 'neutral' : 'warning')">
                            {{ $content['status'] }}
                        </x-ui.badge>
                    </td>
                    <td class="whitespace-nowrap text-secondary-light">
                        {{ $content['published_at'] ?? '—' }}
                    </td>
                    <td class="whitespace-nowrap">
                        <div class="flex flex-nowrap items-center justify-end gap-2">
                            @can('news.update')
                                <x-ui.button
                                    href="{{ route('admin.news.edit', $content['slug']) }}"
                                    variant="secondary"
                                    size="sm"
                                >
                                    Editar
                                </x-ui.button>
                            @endcan

                            @if ($content['status'] === 'publicado')
                                <x-ui.button
                                    href="{{ route('contents.show', $content['slug']) }}"
                                    variant="secondary"
                                    size="sm"
                                >
                                    Ver
                                </x-ui.button>
                            @endif

                            @if ($content['status'] === 'borrador')
                                @can('news.publish')
                                    <form method="POST" action="{{ route('admin.news.publish', $content['slug']) }}">
                                        @csrf
                                        <x-ui.button type="submit" variant="success" size="sm">
                                            Aprobar
                                        </x-ui.button>
                                    </form>
                                @endcan
                            @endif

                            @if ($content['status'] !== 'archivado')
                                @can('news.delete')
                                    <form
                                        method="POST"
                                        action="{{ route('admin.news.destroy', $content['slug']) }}"
                                        onsubmit="return confirm('¿Desaprobar esta noticia? Se archivará y permanecerá en el histórico.')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">
                                            Desaprobar
                                        </x-ui.button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-ui.empty-state embedded title="Sin noticias" description="Cuando se ejecute la extracción aparecerán aquí como borradores." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>
@endsection
