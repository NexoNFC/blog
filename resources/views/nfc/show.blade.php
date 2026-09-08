@extends('layouts.public')

@section('title', 'NFC · '.$point['name'])

@section('content')
    @if ($point['status'] !== 'activo')
        <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
            <x-ui.alert type="warning">
                Este punto NFC no está disponible en este momento. El chip físico puede permanecer instalado; el acceso se controla desde la plataforma.
            </x-ui.alert>

            <div class="mt-6 flex flex-wrap items-center gap-2">
                <h1 class="text-3xl font-bold">{{ $point['name'] }}</h1>
                <x-nfc.point-status :status="$point['status']" />
            </div>
            <p class="mt-2 text-secondary-light">{{ $point['identifier'] }} · {{ $point['location'] }}</p>

            <div class="mt-8">
                <x-ui.button href="{{ route('home') }}" variant="secondary">Ir al inicio</x-ui.button>
            </div>
        </section>
    @else
        <x-nfc.point-header :point="$point" />
        <x-nfc.point-content-section :items="$contents" :more-news="$moreNews" />
    @endif
@endsection
