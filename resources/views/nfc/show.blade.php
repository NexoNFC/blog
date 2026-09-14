@extends('layouts.public')

@section('title', 'NFC · '.$point['name'])

@section('content')
    @if ($point['status'] !== 'activo')
        <section class="mx-auto w-full max-w-screen-2xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
            <div class="glass-card px-6 py-8 sm:px-10">
                <x-ui.alert type="warning">
                    Este punto del campus no está disponible por ahora. Puedes volver al inicio o intentar más tarde.
                </x-ui.alert>

                <div class="mt-6">
                    <h1 class="text-3xl font-bold text-secondary">{{ $point['name'] }}</h1>
                </div>
                @if (! empty($point['location']))
                    <p class="mt-2 text-secondary-light">{{ $point['location'] }}</p>
                @endif

                <div class="mt-8">
                    <x-ui.button href="{{ route('home') }}" variant="secondary">Ir al inicio</x-ui.button>
                </div>
            </div>
        </section>
    @else
        <section class="mx-auto w-full max-w-screen-2xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            <nav class="reveal mb-6 text-sm text-secondary-light" aria-label="Miga de pan">
                <a href="{{ route('home') }}" class="font-medium text-primary hover:underline">Inicio</a>
                <span class="mx-2 text-secondary-light/70">/</span>
                <span class="text-secondary">{{ $point['name'] }}</span>
            </nav>

            <div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10 xl:gap-12">
                <div class="order-2 lg:order-1 lg:col-span-8 xl:col-span-9">
                    <x-nfc.point-content-section :items="$contents" :more-news="$moreNews" />
                </div>

                <aside class="order-1 lg:order-2 lg:col-span-4 xl:col-span-3 lg:sticky lg:top-24">
                    <x-nfc.point-header :point="$point" />
                </aside>
            </div>
        </section>
    @endif
@endsection
