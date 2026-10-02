@extends('layouts.public')

@section('title', $content['title'])

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-12">
        <div class="glass-card px-6 py-8 sm:px-10 sm:py-12">
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <x-content.content-meta :content="$content" />

                @if (! empty($content['external_url']))
                    <x-content.external-source
                        :url="$content['external_url']"
                        label="Ver en el sitio oficial de FESC"
                        size="sm"
                        class="ms-auto"
                    />
                @endif
            </div>

            <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-5xl">
                <span class="gradient-text">{{ $content['title'] }}</span>
            </h1>
            <p class="mt-4 text-lg leading-relaxed text-secondary-light">{{ $content['summary'] }}</p>

            @if (! empty($content['image']))
                <div class="mt-8 overflow-hidden rounded-[20px] border border-white/50 bg-white/40">
                    <img
                        src="{{ $content['image'] }}"
                        alt="{{ $content['title'] }}"
                        class="aspect-[16/9] h-full w-full object-cover"
                        loading="lazy"
                    >
                </div>
            @endif

            @if (! empty($content['gallery']))
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($content['gallery'] as $image)
                        <img
                            src="{{ $image }}"
                            alt="{{ $content['title'] }}"
                            class="aspect-[16/9] w-full rounded-xl object-cover"
                            loading="lazy"
                        >
                    @endforeach
                </div>
            @endif

            <x-content.body :text="$content['body']" class="mt-8 space-y-4 text-base leading-relaxed text-secondary" />

            <div class="mt-12 border-t border-white/40 pt-6">
                <x-ui.button href="{{ route('home') }}" variant="secondary">Volver al inicio</x-ui.button>
            </div>
        </div>
    </article>
@endsection
