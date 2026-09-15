@props([
    'paginator',
])

@if ($paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'admin-pagination mt-6']) }}>
        {{ $paginator->onEachSide(1)->links('pagination.admin') }}
    </div>
@endif
