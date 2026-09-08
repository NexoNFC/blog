@extends('layouts.admin')

@section('title', 'Origen del contenido')
@section('heading', 'Origen del contenido')
@section('subtitle', 'Las noticias no se crean desde cero en este panel')

@section('actions')
    <x-ui.button href="{{ route('admin.news.index') }}" variant="secondary">Volver al listado</x-ui.button>
@endsection

@section('content')
    <x-ui.alert type="info" class="mb-6">
        El flujo principal es extraer contenido desde el portal oficial de FESC y el Instagram institucional,
        generar un borrador y revisarlo aquí antes de publicar. La creación manual desde cero no forma parte de este producto.
    </x-ui.alert>

    <x-ui.card>
        <p class="text-secondary">
            Cuando el proceso automático o la acción «Actualizar ahora» estén disponibles, los borradores aparecerán en el listado de noticias.
        </p>
        <div class="mt-6">
            <x-ui.button href="{{ route('admin.news.index') }}">Ir a noticias</x-ui.button>
        </div>
    </x-ui.card>
@endsection
