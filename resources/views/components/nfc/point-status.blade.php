@props([
    'status',
])

<x-ui.badge :tone="$status === 'activo' ? 'success' : 'danger'">
    {{ $status }}
</x-ui.badge>
