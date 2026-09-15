@extends('layouts.admin')

@section('title', 'Noticias')
@section('heading', 'Noticias')
@section('subtitle', 'Trae piezas del portal FESC, revísalas y decide cuáles se publican')

@section('actions')
    @can('news.create')
        <form
            method="POST"
            action="{{ route('admin.news.ingest') }}"
            x-data="{ sending: false }"
            @submit="sending = true"
        >
            @csrf
            <x-ui.button type="submit" x-bind:disabled="sending">
                <span x-show="!sending">Traer noticias del portal</span>
                <span x-cloak x-show="sending">Extrayendo…</span>
            </x-ui.button>
        </form>
    @endcan
@endsection

@section('content')
    <x-ui.alert type="info" class="mb-6">
        Las noticias se extraen de «Proyectamos Nuestra Institución» y de
        <a href="https://www.fesc.edu.co/portal/news-bienestar" class="font-semibold text-primary hover:underline" target="_blank" rel="noopener noreferrer">News Bienestar</a>,
        <a href="https://www.fesc.edu.co/portal/comunicados" class="font-semibold text-primary hover:underline" target="_blank" rel="noopener noreferrer">Comunicados</a>,
        <a href="https://www.fesc.edu.co/portal/news-sig" class="font-semibold text-primary hover:underline" target="_blank" rel="noopener noreferrer">Novedades SIG</a>
        y
        <a href="https://www.fesc.edu.co/portal/news-extension" class="font-semibold text-primary hover:underline" target="_blank" rel="noopener noreferrer">News Extension</a>.
        Llegan como borrador: tú eliges si se publican tal cual o transcritas con IA.
    </x-ui.alert>

    @if ($lastRun)
        <p class="mb-6 text-sm text-secondary-light">
            Última extracción: {{ $lastRun->finished_at?->format('Y-m-d H:i') ?? 'en curso' }}
            · encontradas {{ $lastRun->contents_found }}
            · nuevas {{ $lastRun->news_created }}
            · estado {{ $lastRun->status->value }}.
            @unless ($aiConfigured)
                La transcripción con IA estará disponible cuando exista AI_API_KEY.
            @endunless
        </p>
    @endif

    <x-ui.table wide>
        <thead>
            <tr>
                <th scope="col">Título</th>
                <th scope="col">Origen</th>
                <th scope="col">Texto</th>
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
                        <x-ui.badge>{{ $content['section'] ?? $content['type'] }}</x-ui.badge>
                    </td>
                    <td class="whitespace-nowrap">
                        <x-ui.badge :tone="$content['presentation'] === 'ai' ? 'info' : 'neutral'">
                            {{ $content['presentation'] === 'ai' ? 'IA' : 'Original' }}
                        </x-ui.badge>
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
                        <div class="ml-auto grid w-max grid-cols-3 gap-2">
                            <div class="min-w-[6.75rem]">
                                @can('news.update')
                                    <x-ui.button
                                        href="{{ route('admin.news.edit', $content['slug']) }}"
                                        variant="secondary"
                                        size="sm"
                                        class="w-full"
                                    >
                                        Revisar
                                    </x-ui.button>
                                @endcan
                            </div>

                            <div class="min-w-[6.75rem]">
                                @if ($content['status'] === 'publicado')
                                    <x-ui.button
                                        href="{{ route('contents.show', $content['slug']) }}"
                                        variant="secondary"
                                        size="sm"
                                        class="w-full"
                                    >
                                        Ver
                                    </x-ui.button>
                                @elseif ($content['status'] === 'borrador')
                                    @can('news.publish')
                                        <form method="POST" action="{{ route('admin.news.publish', $content['slug']) }}" class="w-full">
                                            @csrf
                                            <x-ui.button type="submit" variant="success" size="sm" class="w-full">
                                                Publicar
                                            </x-ui.button>
                                        </form>
                                    @endcan
                                @endif
                            </div>

                            <div class="min-w-[6.75rem]">
                                @if ($content['status'] !== 'archivado')
                                    @can('news.delete')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.news.destroy', $content['slug']) }}"
                                            class="w-full"
                                            onsubmit="return confirm('¿Desaprobar esta noticia? Se archivará y permanecerá en el histórico.')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="danger" size="sm" class="w-full">
                                                Desaprobar
                                            </x-ui.button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-ui.empty-state embedded title="Sin noticias" description="Usa «Traer noticias del portal» para importar borradores desde FESC." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    <x-ui.pagination :paginator="$contents" />
@endsection
