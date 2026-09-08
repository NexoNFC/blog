@extends('layouts.public')

@section('title', $content['title'])

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-12">
        <div class="glass-card px-6 py-8 sm:px-10 sm:py-12">
            <x-content.content-meta :content="$content" />

            <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-5xl">
                <span class="gradient-text">{{ $content['title'] }}</span>
            </h1>
            <p class="mt-4 text-lg leading-relaxed text-secondary-light">{{ $content['summary'] }}</p>

            <div class="mt-8 overflow-hidden rounded-[20px] border border-white/50 bg-white/40">
                @if (! empty($content['image']))
                    <img
                        src="{{ asset($content['image']) }}"
                        alt="{{ $content['title'] }}"
                        class="aspect-[16/9] h-full w-full object-cover"
                        loading="lazy"
                    >
                @endif
            </div>

            <div class="mt-8 space-y-4 text-base leading-relaxed text-secondary">
                @foreach (preg_split("/\n\n/", $content['body']) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>

            @if (! empty($content['external_url']))
                <div class="mt-10 space-y-4">
                    <x-ui.alert type="info">
                        Resumen en esta plataforma. El detalle oficial está en el sitio de FESC.
                    </x-ui.alert>
                    <x-content.external-source :url="$content['external_url']" label="Consultar información oficial" />
                </div>
            @endif

            <div class="mt-12 border-t border-white/40 pt-6">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-primary hover:underline">Volver al inicio</a>
            </div>
        </div>
    </article>
@endsection
