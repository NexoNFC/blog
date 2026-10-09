@extends('layouts.admin')

@section('title', 'Perfil')
@section('heading', 'Perfil')
@section('subtitle', 'Foto, descripción y datos de tu cuenta de administrador')

@section('content')
    <div class="mx-auto grid max-w-5xl items-start gap-5 lg:grid-cols-[17rem_minmax(0,1fr)]">
        <x-ui.card class="text-center lg:sticky lg:top-24">
            <x-admin.avatar :user="$user" size="lg" class="mx-auto" />
            <p class="mt-4 text-lg font-bold tracking-tight text-secondary">{{ $user->name }}</p>
            <p class="mt-1 break-all text-sm text-secondary-light">{{ $user->email }}</p>
            @if (filled($user->bio))
                <p class="mt-3 text-sm leading-relaxed text-secondary-light">{{ $user->bio }}</p>
            @endif
            <p class="mt-4 inline-flex rounded-full bg-primary-soft px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-primary">
                Administrador
            </p>
        </x-ui.card>

        <div class="space-y-5">
            <x-ui.card>
                @include('profile.partials.update-profile-information-form')
            </x-ui.card>

            <x-ui.card>
                @include('profile.partials.update-password-form')
            </x-ui.card>

            <x-ui.card class="!border-danger/20">
                @include('profile.partials.delete-user-form')
            </x-ui.card>
        </div>
    </div>
@endsection
