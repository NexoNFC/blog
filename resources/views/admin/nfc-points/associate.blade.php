@extends('layouts.admin')

@section('title', 'Asociar noticia · '.$point->name)
@section('heading', $point->name)
@section('subtitle', 'Elige la noticia activa para /nfc/'.$point->code)

@section('actions')
    <x-ui.button href="{{ route('admin.nfc.index') }}" variant="secondary" class="!rounded-2xl">Volver a puntos NFC</x-ui.button>
@endsection

@section('content')
    <div x-data="nfcAssociateSearch" class="space-y-5">
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
                        <span class="font-bold">{{ $point->news?->title ?? 'Sin noticia asociada' }}</span>
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
                        placeholder="Filtrar por título o resumen"
                        autocomplete="off"
                        x-model="query"
                        @keydown.escape.prevent="query = ''"
                    />
                </div>
                <x-ui.button
                    type="button"
                    variant="secondary"
                    class="!rounded-2xl"
                    x-show="query.trim() !== ''"
                    x-cloak
                    @click="query = ''"
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
                @forelse ($news as $item)
                    <tr
                        data-nfc-news-row
                        data-search="{{ \Illuminate\Support\Str::lower(($item->title ?? '').' '.($item->summary ?? '')) }}"
                        @class(['bg-primary-soft/40' => (int) $point->news_id === (int) $item->id])
                        x-show="matches($el.dataset.search)"
                    >
                        <th scope="row" class="min-w-[16rem] max-w-xl">
                            <p class="leading-snug">{{ $item->title }}</p>
                            @if (filled($item->summary))
                                <p class="mt-0.5 line-clamp-2 text-xs font-normal text-secondary-light">{{ $item->summary }}</p>
                            @endif
                        </th>
                        <td class="whitespace-nowrap text-secondary-light">
                            {{ $item->origin_published_at?->toDateString() ?? $item->published_at?->toDateString() ?? '—' }}
                        </td>
                        <td class="text-end">
                            @if ((int) $point->news_id === (int) $item->id)
                                <span class="inline-flex rounded-full bg-primary-soft px-3 py-1 text-xs font-bold uppercase tracking-[0.12em] text-primary">
                                    Asociada
                                </span>
                            @else
                                <form method="POST" action="{{ route('admin.nfc.update', $point) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="news_id" value="{{ $item->id }}">
                                    <input type="hidden" name="_return" value="{{ route('admin.nfc.associate', $point) }}">
                                    <x-ui.button type="submit" size="sm" variant="secondary">Asociar</x-ui.button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                @endforelse

                <tr x-show="!hasResults" x-cloak>
                    <td colspan="3">
                        <x-ui.empty-state
                            embedded
                            title="Sin resultados"
                            description="No hay noticias publicadas que coincidan con la búsqueda."
                        />
                    </td>
                </tr>

                @if ($news->isEmpty())
                    <tr>
                        <td colspan="3">
                            <x-ui.empty-state
                                embedded
                                title="Sin resultados"
                                description="Todavía no hay noticias publicadas para asociar."
                            />
                        </td>
                    </tr>
                @endif
            </tbody>
        </x-ui.table>

        @if ($point->news_id)
            <form method="POST" action="{{ route('admin.nfc.update', $point) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="news_id" value="">
                <input type="hidden" name="_return" value="{{ route('admin.nfc.associate', $point) }}">
                <x-ui.button type="submit" variant="danger" class="!rounded-2xl">Quitar noticia asociada</x-ui.button>
            </form>
        @endif
    </div>
@endsection
