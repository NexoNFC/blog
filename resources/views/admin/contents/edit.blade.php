@extends('layouts.admin')

@section('title', 'Revisar noticia')
@section('heading', 'Revisar noticia')
@section('subtitle', 'Elige el texto original o la transcripción con IA. Solo se publica si la apruebas.')

@section('actions')
    <x-ui.button href="{{ route('admin.news.index') }}" variant="secondary">Volver al listado</x-ui.button>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        @if ($images !== [])
            <x-ui.card class="space-y-3">
                <h2 class="text-sm font-semibold text-secondary">Imágenes extraídas</h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($images as $image)
                        <img
                            src="{{ $image }}"
                            alt="{{ $news->title }}"
                            class="aspect-[16/9] w-full rounded-xl object-cover"
                            loading="lazy"
                        >
                    @endforeach
                </div>
            </x-ui.card>
        @endif

        <x-ui.card class="space-y-4">
            <p class="text-sm text-secondary-light">
                Presentación actual:
                <strong>{{ $presentation === 'ai' ? 'transcripción con IA' : 'texto original del portal' }}</strong>.
                @if ($news->origin_url)
                    <a href="{{ $news->origin_url }}" class="font-semibold text-primary hover:underline" target="_blank" rel="noopener noreferrer">Ver fuente oficial</a>
                @endif
            </p>

            <div class="flex flex-wrap gap-3">
                @can('news.update')
                    <form method="POST" action="{{ route('admin.news.rewrite', $news) }}">
                        @csrf
                        @if ($aiConfigured)
                            <x-ui.button type="submit">Transcribir con IA</x-ui.button>
                        @else
                            <x-ui.button type="submit" disabled>Transcribir con IA</x-ui.button>
                        @endif
                    </form>

                    <form method="POST" action="{{ route('admin.news.original', $news) }}">
                        @csrf
                        <x-ui.button type="submit" variant="secondary">
                            Presentar original
                        </x-ui.button>
                    </form>
                @endcan
            </div>

            @unless ($aiConfigured)
                <p class="text-sm text-secondary-light">Para transcribir con IA configura AI_API_KEY en el entorno.</p>
            @endunless
        </x-ui.card>

        <form method="POST" action="{{ route('admin.news.update', $news) }}" class="space-y-6">
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

        @if ($news->status->value === 'borrador')
            @can('news.publish')
                <form method="POST" action="{{ route('admin.news.publish', $news) }}">
                    @csrf
                    <x-ui.button type="submit" variant="success">Publicar</x-ui.button>
                </form>
            @endcan
        @endif
    </div>
@endsection
