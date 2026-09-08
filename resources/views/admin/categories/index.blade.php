@extends('layouts.admin')

@section('title', 'Categorías')
@section('heading', 'Categorías')
@section('subtitle', 'Clasificación de contenido')

@section('content')
    <x-ui.alert type="info" class="mb-6">
        Las categorías se usan al revisar borradores. El alta y edición completa se habilitará más adelante.
    </x-ui.alert>

    <x-ui.table>
        <thead class="border-b border-white/40 bg-white/30 text-xs uppercase tracking-wide text-secondary-light">
            <tr>
                <th class="px-4 py-3 font-semibold">Nombre</th>
                <th class="px-4 py-3 font-semibold">Identificador</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/40">
            @forelse ($categories as $category)
                <tr class="transition hover:bg-white/35">
                    <td class="px-4 py-3 font-medium text-secondary">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-secondary-light">{{ $category->slug }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-4 py-8">
                        <x-ui.empty-state title="Sin categorías" description="Ejecuta los seeders para cargar las categorías iniciales." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @can('categories.create')
        <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-6">
            @csrf
            <x-ui.button type="submit" variant="secondary">Registrar categoría (preparado)</x-ui.button>
        </form>
    @endcan
@endsection
