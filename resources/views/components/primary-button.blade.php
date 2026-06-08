<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-lg border border-transparent bg-[#06488f] px-5 py-2.5 text-xs font-extrabold uppercase tracking-widest text-white shadow-sm shadow-sky-100 transition hover:bg-[#053a73] focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 active:scale-95 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
