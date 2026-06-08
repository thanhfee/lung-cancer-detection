@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-black uppercase tracking-widest text-slate-500']) }}>
    {{ $value ?? $slot }}
</label>
