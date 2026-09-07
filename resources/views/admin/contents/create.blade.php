@extends('layouts.admin')

@section('title', 'Nueva noticia')
@section('heading', 'Crear contenido')
@section('subtitle', 'Formulario de maqueta — no guarda datos')

@section('actions')
    <x-ui.button href="{{ route('admin.news.index') }}" variant="secondary">Volver al listado</x-ui.button>
@endsection

@section('content')
    <x-ui.alert type="warning" class="mb-6">
        Este formulario es solo visual. Al enviar no se persiste información.
    </x-ui.alert>

    <form action="#" method="get" class="mx-auto max-w-3xl space-y-6" onsubmit="return false;">
        <x-ui.card class="space-y-6">
        <x-ui.input label="Título" name="title" required placeholder="Ej. Convocatoria de bienestar universitario" />

        <x-ui.textarea label="Resumen" name="summary" required rows="3" placeholder="Texto breve para listados y experiencia NFC" />

        <x-ui.textarea label="Cuerpo" name="body" required rows="8" placeholder="Contenido principal de la publicación" />

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1.5">
                <label for="type" class="block text-sm font-medium text-secondary">Tipo <span class="text-primary">*</span></label>
                <select id="type" name="type" class="form-control">
                    <option value="noticia">Noticia</option>
                    <option value="comunicado">Comunicado</option>
                    <option value="evento">Evento</option>
                    <option value="institucional">Institucional</option>
                    <option value="externo">Externo</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="status" class="block text-sm font-medium text-secondary">Estado <span class="text-primary">*</span></label>
                <select id="status" name="status" class="form-control">
                    <option value="borrador">Borrador</option>
                    <option value="publicado">Publicado</option>
                </select>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-ui.input label="Fecha de publicación" name="published_at" type="date" />
            <x-ui.input label="Imagen destacada" name="image" type="file" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-ui.input label="Inicio del evento" name="event_starts_at" type="datetime-local" />
            <x-ui.input label="Fin del evento" name="event_ends_at" type="datetime-local" />
        </div>

        <x-ui.input label="Enlace externo (opcional)" name="external_url" type="url" placeholder="https://fesc.edu.co/..." />

        <x-ui.input label="Categoría o etiquetas" name="tags" placeholder="Ej. bienestar, académico" />

        <div class="flex flex-wrap gap-3 border-t border-white/40 pt-5">
            <x-ui.button type="submit">Guardar (demo)</x-ui.button>
            <x-ui.button href="{{ route('admin.news.index') }}" variant="secondary">Cancelar</x-ui.button>
        </div>
        </x-ui.card>
    </form>
@endsection
