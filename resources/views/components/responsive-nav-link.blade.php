@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#7D5A50] text-start text-base font-medium text-[#7D5A50] bg-[#7D5A50]/10 focus:outline-none focus:text-[#5a3f38] focus:bg-[#7D5A50]/15 focus:border-[#5a3f38] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-[#7D5A50]/40 focus:outline-none focus:text-slate-800 focus:bg-slate-50 focus:border-[#7D5A50]/40 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
