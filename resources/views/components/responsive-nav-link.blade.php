@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg bg-sky-50 px-4 py-2 text-start text-base font-extrabold text-[#06488f] ring-1 ring-sky-100 transition duration-150 ease-in-out'
            : 'block w-full rounded-lg px-4 py-2 text-start text-base font-bold text-slate-600 hover:bg-sky-50 hover:text-[#06488f] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
