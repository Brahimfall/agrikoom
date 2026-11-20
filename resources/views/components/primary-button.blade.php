<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#7D5A50] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#6b4d44] focus:bg-[#6b4d44] active:bg-[#5a3f38] focus:outline-none focus:ring-2 focus:ring-[#7D5A50] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
