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
                    <td>
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            @can('update', $category)
                                <x-ui.button href="{{ route('admin.categories.edit', $category) }}" variant="secondary" size="sm">
                                    Editar
                                </x-ui.button>
                            @endcan

                            @can('delete', $category)
                                @if ($category->news_count > 0)
                                    <span class="text-xs text-secondary-light">En uso</span>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('admin.categories.destroy', $category) }}"
                                        onsubmit="return confirm('¿Eliminar esta categoría? Esta acción no se puede deshacer.')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">
                                            Eliminar
                                        </x-ui.button>
                                    </form>
                                @endif
                            @endcan
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
