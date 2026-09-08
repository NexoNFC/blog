@extends('layouts.public')

@section('title', $content['title'])

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-12">
        <x-content.content-meta :content="$content" />

        <h1 class="mt-4 text-3xl font-bold sm:text-4xl">{{ $content['title'] }}</h1>
        <p class="mt-4 text-lg text-secondary-light">{{ $content['summary'] }}</p>

        <div class="mt-8 aspect-[16/9] overflow-hidden rounded-2xl border border-white/50 bg-white/40 backdrop-blur-md">
            @if (! empty($content['image']))
                <img
                    src="{{ asset($content['image']) }}"
                    alt="{{ $content['title'] }}"
                    class="h-full w-full object-cover"
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

        <div class="mt-12 border-t border-muted pt-6">
            <a href="{{ route('home') }}" class="text-sm font-semibold text-primary hover:underline">Volver al inicio</a>
        </div>
    </article>
@endsection
