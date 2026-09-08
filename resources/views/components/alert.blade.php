@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
    'dismissible' => false,
    'timeout' => null,
])

<x-ui.alert
    :type="$type"
    :title="$title"
    :message="$message"
    :dismissible="$dismissible"
    :timeout="$timeout"
    {{ $attributes }}
>{{ $slot }}</x-ui.alert>
