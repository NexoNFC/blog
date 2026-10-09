@props([
    'point',
])

@php
    $inputId = 'news_id_'.$point['code'];
    $newsOptions = collect($point['news_options'] ?? []);
@endphp

@can('nfc.manage-content')
    <form
        method="POST"
        action="{{ route('admin.nfc.update', $point['code']) }}"
        class="nfc-associate-form"
        x-on:submit.prevent="associate($event, {{ \Illuminate\Support\Js::from($point['code']) }})"
    >
        @csrf
        @method('PATCH')
        <label class="sr-only" for="{{ $inputId }}">Noticia para {{ $point['name'] }}</label>
        <x-form.select id="{{ $inputId }}" name="news_id" class="nfc-associate-form__select">
            <option value="">Sin noticia</option>
            @foreach ($newsOptions as $item)
                @php
                    $optionId = is_array($item) ? ($item['id'] ?? null) : $item->id;
                    $optionTitle = is_array($item) ? ($item['title'] ?? '') : $item->title;
                @endphp
                <option value="{{ $optionId }}" @selected((int) ($point['news_id'] ?? 0) === (int) $optionId)>
                    {{ $optionTitle }}
                </option>
            @endforeach
            <option value="__browse__" data-url="{{ route('admin.nfc.associate', $point['code']) }}">
                Buscar en todas las noticias…
            </option>
        </x-form.select>
        <x-ui.button
            type="submit"
            size="sm"
            variant="secondary"
            class="nfc-associate-form__submit"
            x-bind:disabled="isBusy({{ \Illuminate\Support\Js::from($point['code']) }})"
        >
            <span x-show="! isBusy({{ \Illuminate\Support\Js::from($point['code']) }})">Asociar</span>
            <span x-cloak x-show="isBusy({{ \Illuminate\Support\Js::from($point['code']) }})">…</span>
        </x-ui.button>
    </form>
@else
    <p class="truncate text-sm text-secondary-light" title="{{ $point['news_title'] ?? 'Sin noticia' }}">
        {{ $point['news_title'] ?? 'Sin noticia' }}
    </p>
@endcan
