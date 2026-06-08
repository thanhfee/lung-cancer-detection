@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-0 bg-slate-50 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-100 placeholder:text-slate-400 focus:ring-2 focus:ring-sky-500 disabled:opacity-50']) }}>
