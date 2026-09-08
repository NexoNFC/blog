@extends('layouts.admin')

@section('title', 'Editar noticia')
@section('heading', 'Revisar noticia')
@section('subtitle', 'Corrige el borrador o el contenido publicado sin perder la trazabilidad de origen')

@section('actions')
    <x-ui.button href="{{ route('admin.news.index') }}" variant="secondary">Volver al listado</x-ui.button>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.news.update', $news) }}" class="mx-auto max-w-3xl space-y-6">
        @csrf
        @method('PATCH')

        <x-ui.card class="space-y-6">
            <x-ui.input
                label="Título"
                name="title"
                required
                :value="old('title', $news->title)"
            />

            <x-ui.textarea label="Resumen" name="summary" required rows="3">{{ old('summary', $news->summary) }}</x-ui.textarea>

            <x-ui.textarea label="Contenido" name="body" required rows="10">{{ old('body', $news->body) }}</x-ui.textarea>

            <div class="space-y-1.5">
                <label for="category_id" class="block text-sm font-medium text-secondary">Categoría</label>
                <select id="category_id" name="category_id" class="form-control" @if ($errors->has('category_id')) aria-invalid="true" @endif>
                    <option value="">Sin categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $news->category_id) === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <x-ui.input
                label="URL original"
                name="origin_url"
                type="url"
                :value="old('origin_url', $news->origin_url)"
                placeholder="https://www.fesc.edu.co/..."
            />

            <p class="text-sm text-secondary-light">
                Estado actual: {{ $news->status->value }}.
                @if ($news->admin_edited_at)
                    Última edición administrativa: {{ $news->admin_edited_at->format('Y-m-d H:i') }}.
                @endif
            </p>

            <div class="flex flex-wrap gap-3 border-t border-white/40 pt-5">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
            </div>
        </x-ui.card>
    </form>

    @if ($news->status->value !== 'publicado')
        @can('news.publish')
            <form method="POST" action="{{ route('admin.news.publish', $news) }}" class="mx-auto mt-4 max-w-3xl">
                @csrf
                <x-ui.button type="submit" variant="secondary">Publicar</x-ui.button>
            </form>
        @endcan
    @endif
@endsection
