@extends('layouts.admin')

@section('title', 'Noticias')
@section('heading', 'Noticias')
@section('subtitle', 'Trae piezas del portal FESC, revísalas y decide cuáles se publican')

@php
    $newsIndexConfig = [
        'items' => array_values($contents->items()),
        'lastRun' => $lastRun ? [
            'id' => $lastRun->id,
            'status' => $lastRun->status->value,
            'contents_found' => $lastRun->contents_found,
            'news_created' => $lastRun->news_created,
            'finished_at' => $lastRun->finished_at?->format('Y-m-d H:i'),
            'error_message' => $lastRun->error_message,
        ] : null,
        'aiConfigured' => $aiConfigured,
        'can' => [
            'create' => auth()->user()?->can('news.create') ?? false,
            'update' => auth()->user()?->can('news.update') ?? false,
            'publish' => auth()->user()?->can('news.publish') ?? false,
            'delete' => auth()->user()?->can('news.delete') ?? false,
        ],
        'routes' => [
            'index' => route('admin.news.index'),
            'ingest' => route('admin.news.ingest'),
        ],
    ];
@endphp

@section('content')
    <div
        x-data="adminNewsIndex({{ \Illuminate\Support\Js::from($newsIndexConfig) }})"
        class="space-y-6"
    >
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <x-ui.alert type="info" class="mb-0 flex-1">
                Las noticias se extraen de «Proyectamos Nuestra Institución» y de
                <a href="https://www.fesc.edu.co/portal/news-bienestar" class="font-semibold text-primary hover:underline" target="_blank" rel="noopener noreferrer">News Bienestar</a>,
                <a href="https://www.fesc.edu.co/portal/comunicados" class="font-semibold text-primary hover:underline" target="_blank" rel="noopener noreferrer">Comunicados</a>,
                <a href="https://www.fesc.edu.co/portal/news-sig" class="font-semibold text-primary hover:underline" target="_blank" rel="noopener noreferrer">Novedades SIG</a>
                y
                <a href="https://www.fesc.edu.co/portal/news-extension" class="font-semibold text-primary hover:underline" target="_blank" rel="noopener noreferrer">News Extension</a>.
                Llegan como borrador: tú eliges si se publican tal cual o transcritas con IA.
            </x-ui.alert>

            <template x-if="can.create">
                <div class="shrink-0">
                    <x-ui.button type="button" x-bind:disabled="ingesting" x-on:click="ingest()">
                        <span x-show="!ingesting">Traer noticias del portal</span>
                        <span x-cloak x-show="ingesting">Extrayendo…</span>
                    </x-ui.button>
                </div>
            </template>
        </div>

        <p class="text-sm text-secondary-light" x-show="lastRun">
            Última extracción:
            <span x-text="lastRun?.finished_at || 'en curso'"></span>
            · encontradas <span x-text="lastRun?.contents_found ?? 0"></span>
            · nuevas <span x-text="lastRun?.news_created ?? 0"></span>
            · estado <span x-text="lastRun?.status || '—'"></span>.
            <span x-show="!aiConfigured">La transcripción con IA estará disponible cuando exista AI_API_KEY.</span>
        </p>

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
                <template x-for="item in items" :key="item.slug">
                    <tr>
                        <th scope="row" class="min-w-[16rem] max-w-sm">
                            <p class="leading-snug" x-text="item.title"></p>
                            <p class="mt-0.5 truncate text-xs font-normal text-secondary-light" x-text="item.summary"></p>
                        </th>
                        <td class="whitespace-nowrap">
                            <span class="admin-badge admin-badge--neutral" x-text="item.section || item.type"></span>
                        </td>
                        <td class="whitespace-nowrap">
                            <span
                                class="admin-badge"
                                :class="presentationTone(item.presentation)"
                                x-text="presentationLabel(item.presentation)"
                            ></span>
                        </td>
                        <td class="whitespace-nowrap">
                            <span
                                class="admin-badge"
                                :class="statusTone(item.status)"
                                x-text="item.status"
                            ></span>
                        </td>
                        <td class="whitespace-nowrap text-secondary-light" x-text="item.published_at || '—'"></td>
                        <td class="whitespace-nowrap">
                            <div class="ml-auto grid w-max grid-cols-3 gap-2">
                                <div class="min-w-[6.75rem]">
                                    <template x-if="can.update">
                                        <a
                                            :href="item.urls.edit"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-secondary/15 bg-white/70 px-3 py-1.5 text-sm font-bold tracking-tight text-secondary transition hover:bg-white"
                                        >
                                            Revisar
                                        </a>
                                    </template>
                                </div>

                                <div class="min-w-[6.75rem]">
                                    <template x-if="item.status === 'publicado' && item.urls.show">
                                        <a
                                            :href="item.urls.show"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-secondary/15 bg-white/70 px-3 py-1.5 text-sm font-bold tracking-tight text-secondary transition hover:bg-white"
                                        >
                                            Ver
                                        </a>
                                    </template>
                                    <template x-if="item.status === 'borrador' && can.publish">
                                        <button
                                            type="button"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-success px-3 py-1.5 text-sm font-bold tracking-tight text-white transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50"
                                            x-bind:disabled="isBusy(item.slug)"
                                            x-on:click="publish(item)"
                                        >
                                            <span x-text="isBusy(item.slug) ? 'Publicando…' : 'Publicar'"></span>
                                        </button>
                                    </template>
                                </div>

                                <div class="min-w-[6.75rem]">
                                    <template x-if="item.status !== 'archivado' && can.delete">
                                        <button
                                            type="button"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-danger px-3 py-1.5 text-sm font-bold tracking-tight text-white transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50"
                                            x-bind:disabled="isBusy(item.slug)"
                                            x-on:click="archive(item)"
                                        >
                                            <span x-text="isBusy(item.slug) ? '…' : 'Desaprobar'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="items.length === 0">
                    <td colspan="6">
                        <x-ui.empty-state embedded title="Sin noticias" description="Usa «Traer noticias del portal» para importar borradores desde FESC." />
                    </td>
                </tr>
            </tbody>
        </x-ui.table>

        <x-ui.pagination :paginator="$contents" />
    </div>
@endsection
