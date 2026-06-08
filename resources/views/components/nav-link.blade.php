@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-lg bg-white px-4 py-2 text-sm font-extrabold leading-5 text-[#06488f] shadow-sm ring-1 ring-sky-100 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center rounded-lg px-4 py-2 text-sm font-extrabold leading-5 text-[#06488f] hover:bg-white/70 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
