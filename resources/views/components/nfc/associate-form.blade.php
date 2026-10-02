@props([
    'point',
])

@php
    $inputId = 'news_id_'.$point['code'];
    $newsOptions = collect($point['news_options'] ?? []);
@endphp

@can('nfc.manage-content')
    <form method="POST" action="{{ route('admin.nfc.update', $point['code']) }}" class="flex min-w-0 items-center gap-2">
        @csrf
        @method('PATCH')
        <label class="sr-only" for="{{ $inputId }}">Noticia para {{ $point['name'] }}</label>
        <x-form.select id="{{ $inputId }}" name="news_id" class="min-w-0 flex-1">
            <option value="">Sin noticia</option>
            @foreach ($newsOptions as $item)
                <option value="{{ $item->id }}" @selected((int) ($point['news_id'] ?? 0) === (int) $item->id)>
                    {{ $item->title }}
                </option>
            @endforeach
            <option value="__browse__" data-url="{{ route('admin.nfc.associate', $point['code']) }}">
                Buscar en todas las noticias…
            </option>
        </x-form.select>
        <x-ui.button type="submit" size="sm" variant="secondary" class="shrink-0">Asociar</x-ui.button>
    </form>
@else
    <p class="text-sm text-secondary-light">{{ $point['news_title'] ?? 'Sin noticia' }}</p>
@endcan
