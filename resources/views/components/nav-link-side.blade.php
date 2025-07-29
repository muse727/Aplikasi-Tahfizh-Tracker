@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-6 py-3 text-sm font-semibold text-white bg-emerald-600'
            : 'flex items-center px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>