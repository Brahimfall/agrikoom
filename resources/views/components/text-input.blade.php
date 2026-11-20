@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-[#7D5A50] focus:ring-[#7D5A50] rounded-md shadow-sm']) }}>
