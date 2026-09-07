@extends('layouts.admin')

@section('title', 'Contenidos')
@section('heading', 'Contenidos')
@section('subtitle', 'Listado editorial de ejemplo')

@section('actions')
    <x-ui.button href="{{ route('admin.contents.create') }}">Nueva noticia</x-ui.button>
@endsection

@section('content')
    <div class="overflow-hidden rounded border border-muted bg-surface">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-muted bg-background text-xs uppercase tracking-wide text-secondary-light">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Título</th>
                        <th class="px-4 py-3 font-semibold">Tipo</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Publicación</th>
                        <th class="px-4 py-3 font-semibold"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-muted">
                    @foreach ($contents as $content)
                        <tr class="hover:bg-background/80">
                            <td class="px-4 py-3">
                                <p class="font-medium text-secondary">{{ $content['title'] }}</p>
                                <p class="mt-0.5 max-w-md truncate text-xs text-secondary-light">{{ $content['summary'] }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge>{{ $content['type'] }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge :tone="$content['status'] === 'publicado' ? 'success' : 'warning'">{{ $content['status'] }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-secondary-light">
                                {{ $content['published_at'] ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($content['status'] === 'publicado')
                                    <a href="{{ route('contents.show', $content['slug']) }}" class="font-semibold text-primary hover:underline">Ver</a>
                                @else
                                    <span class="text-secondary-light">Borrador</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
