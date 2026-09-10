@extends('layouts.admin')

@section('title', 'Nueva categoría')
@section('heading', 'Registrar categoría')
@section('subtitle', 'El identificador se genera automáticamente a partir del nombre')

@section('actions')
    <x-ui.button href="{{ route('admin.categories.index') }}" variant="secondary">Volver</x-ui.button>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.categories.store') }}" class="mx-auto max-w-xl space-y-5" novalidate>
        @csrf

        <x-ui.card class="space-y-5">
            @include('admin.categories._form')

            <div class="flex flex-wrap gap-3 border-t border-white/40 pt-5">
                <x-ui.button type="submit">Guardar categoría</x-ui.button>
                <x-ui.button href="{{ route('admin.categories.index') }}" variant="secondary">Cancelar</x-ui.button>
            </div>
        </x-ui.card>
    </form>
@endsection
