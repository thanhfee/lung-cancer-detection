<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-lg border border-sky-200 bg-white px-5 py-2.5 text-xs font-extrabold uppercase tracking-widest text-[#06488f] shadow-sm transition hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
