@extends('layouts.admin')

@section('title', 'Editar usuario')
@section('heading', 'Editar usuario')
@section('subtitle', 'Actualiza los datos, el rol o el estado de la cuenta')

@section('actions')
    <x-ui.button href="{{ route('admin.users.index') }}" variant="secondary">Volver</x-ui.button>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mx-auto max-w-xl space-y-5" novalidate>
        @csrf
        @method('PATCH')

        <x-ui.card class="space-y-5">
        @include('admin.users._form')

        <div class="flex flex-wrap gap-3 border-t border-white/40 pt-5">
            <x-ui.button type="submit">Guardar cambios</x-ui.button>
            <x-ui.button href="{{ route('admin.users.index') }}" variant="secondary">Cancelar</x-ui.button>
        </div>
        </x-ui.card>
    </form>
@endsection
