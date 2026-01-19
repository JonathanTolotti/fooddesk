@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'block mx-2 px-3 py-2 text-sm text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-600 rounded-md'
            : 'block mx-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-600/50 rounded-md transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>