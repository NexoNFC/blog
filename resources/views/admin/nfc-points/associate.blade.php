@extends('layouts.admin')

@section('title', 'Asociar noticia · '.$point->name)
@section('heading', $point->name)
@section('subtitle', 'Elige la noticia activa para /nfc/'.$point->code)

@section('actions')
    <x-ui.button href="{{ route('admin.nfc.index') }}" variant="secondary" class="!rounded-2xl">Volver a puntos NFC</x-ui.button>
@endsection

@section('content')
    <div
        x-data="adminNfcAssociate({{ \Illuminate\Support\Js::from($associateConfig) }})"
        class="space-y-5"
    >
        <x-ui.card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary">Punto NFC</p>
                    <p class="mt-1 text-sm text-secondary-light">
                        {{ $point->identifier }}
                        @if ($point->location !== $point->name)
                            · {{ $point->location }}
                        @endif
                    </p>
                    <p class="mt-3 text-sm text-secondary">
                        Noticia actual:
                        <span class="font-bold" x-text="point.news_title || 'Sin noticia asociada'"></span>
                    </p>
                </div>
                <x-nfc.point-status :status="$point->status->value" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1">
                    <x-form.label for="nfc-news-search">Buscar noticia</x-form.label>
                    <x-form.input
                        id="nfc-news-search"
                        name="nfc_news_search"
                        type="search"
                        placeholder="Filtrar por título o resumen"
                        autocomplete="off"
                        x-model="query"
                        x-on:input="onSearchInput()"
                        x-on:keydown.escape.prevent="clearSearch()"
                    />
                </div>
                <x-ui.button
                    type="button"
                    variant="secondary"
                    class="!rounded-2xl"
                    x-show="query.trim() !== ''"
                    x-cloak
                    x-on:click="clearSearch()"
                >
                    Limpiar
                </x-ui.button>
            </div>
            <p class="mt-2 text-xs text-secondary-light" x-text="resultLabel"></p>
        </x-ui.card>

        <x-ui.table>
            <thead>
                <tr>
                    <th scope="col">Noticia</th>
                    <th scope="col">Publicación</th>
                    <th scope="col" class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="item in items" :key="item.id">
                    <tr :class="{ 'bg-primary-soft/40': item.is_associated }">
                        <th scope="row" class="min-w-[16rem] max-w-xl">
                            <p class="leading-snug" x-text="item.title"></p>
                            <p
                                class="mt-0.5 line-clamp-2 text-xs font-normal text-secondary-light"
                                x-show="item.summary"
                                x-text="item.summary"
                            ></p>
                        </th>
                        <td class="whitespace-nowrap text-secondary-light" x-text="item.published_on || '—'"></td>
                        <td class="text-end">
                            <template x-if="item.is_associated">
                                <span class="inline-flex rounded-full bg-primary-soft px-3 py-1 text-xs font-bold uppercase tracking-[0.12em] text-primary">
                                    Asociada
                                </span>
                            </template>
                            <template x-if="! item.is_associated">
                                <form method="POST" :action="updateUrl" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="news_id" :value="item.id">
                                    <input type="hidden" name="_return" :value="returnUrl">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold tracking-tight transition duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 px-3.5 py-2 text-xs border border-secondary/15 bg-white text-secondary shadow-sm hover:border-secondary/25 hover:bg-secondary/5"
                                        x-bind:disabled="loading"
                                    >
                                        Asociar
                                    </button>
                                </form>
                            </template>
                        </td>
                    </tr>
                </template>

                <tr x-show="! loading && items.length === 0" x-cloak>
                    <td colspan="3">
                        <x-ui.empty-state
                            embedded
                            title="Sin resultados"
                            description="No hay noticias publicadas que coincidan con la búsqueda."
                        />
                    </td>
                </tr>
            </tbody>
        </x-ui.table>

        <x-ui.pagination ajax />

        <template x-if="point.news_id">
            <form method="POST" :action="updateUrl">
                @csrf
                @method('PATCH')
                <input type="hidden" name="news_id" value="">
                <input type="hidden" name="_return" :value="returnUrl">
                <x-ui.button type="submit" variant="danger" class="!rounded-2xl">Quitar noticia asociada</x-ui.button>
            </form>
        </template>
    </div>
@endsection
