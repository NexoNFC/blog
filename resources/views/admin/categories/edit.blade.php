@extends('layouts.admin')

@section('title', 'Editar categoría')
@section('heading', 'Editar categoría')
@section('subtitle', 'El identificador no cambia para no romper el catálogo ya clasificado')

@section('actions')
    <x-ui.button href="{{ route('admin.categories.index') }}" variant="secondary">Volver</x-ui.button>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="mx-auto max-w-xl space-y-5" novalidate>
        @csrf
        @method('PATCH')

        <x-ui.card class="space-y-5">
            @include('admin.categories._form')

            <div>
                <x-form.label for="slug">Identificador</x-form.label>
                <x-form.input id="slug" name="slug" :value="$category->slug" disabled />
            </div>

            <div class="flex flex-wrap gap-3 border-t border-white/40 pt-5">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button href="{{ route('admin.categories.index') }}" variant="secondary">Cancelar</x-ui.button>
            </div>
        </x-ui.card>
    </form>
@endsection
