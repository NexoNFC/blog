@extends('layouts.admin')

@section('title', 'Categorías')
@section('heading', 'Categorías')
@section('subtitle', 'Clasificación de contenido')

@section('content')
    <x-ui.alert type="info">
        Listado preparado. El CRUD de categorías se implementará en una tarea posterior.
    </x-ui.alert>

    @can('categories.create')
        <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-6">
            @csrf
            <x-ui.button type="submit">Registrar categoría (preparado)</x-ui.button>
        </form>
    @endcan
@endsection
