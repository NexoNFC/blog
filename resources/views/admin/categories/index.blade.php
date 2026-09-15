@extends('layouts.admin')

@section('title', 'Categorías')
@section('heading', 'Categorías')
@section('subtitle', 'Clasificación de contenido')

@section('actions')
    @can('categories.create')
        <x-ui.button href="{{ route('admin.categories.create') }}" variant="secondary">Nueva categoría</x-ui.button>
    @endcan
@endsection

@section('content')
    <x-ui.alert type="info" class="mb-6">
        Las categorías se usan al revisar borradores y al clasificar el catálogo ingerido.
    </x-ui.alert>

    <x-ui.table>
        <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Identificador</th>
                <th scope="col">Noticias</th>
                <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <th scope="row">{{ $category->name }}</th>
                    <td class="text-secondary-light">{{ $category->slug }}</td>
                    <td class="whitespace-nowrap text-secondary-light">{{ $category->news_count }}</td>
                    <td class="text-end">
                        <div class="inline-grid grid-cols-2 gap-2">
                            <div class="min-w-[6.5rem]">
                                @can('update', $category)
                                    <x-ui.button href="{{ route('admin.categories.edit', $category) }}" variant="secondary" size="sm" class="w-full">
                                        Editar
                                    </x-ui.button>
                                @endcan
                            </div>

                            <div class="flex min-w-[6.5rem] items-center justify-center">
                                @can('delete', $category)
                                    @if ($category->news_count > 0)
                                        <span class="text-xs font-semibold text-secondary-light">
                                            En uso
                                        </span>
                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route('admin.categories.destroy', $category) }}"
                                            class="w-full"
                                            onsubmit="return confirm('¿Eliminar esta categoría? Esta acción no se puede deshacer.')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="danger" size="sm" class="w-full">
                                                Eliminar
                                            </x-ui.button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <x-ui.empty-state embedded title="Sin categorías" description="Registra la primera categoría para clasificar el contenido." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>
@endsection
