@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-md border border-[#7D5A50]/30 bg-[#7D5A50]/10 px-4 py-3 text-sm font-semibold text-[#5a3f38]']) }}>
        {{ $status }}
    </div>
@endif
