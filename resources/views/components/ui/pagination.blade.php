@props([
    'paginator',
])

<div {{ $attributes->merge(['class' => 'mt-6']) }}>
    {{ $paginator->links() }}
</div>
