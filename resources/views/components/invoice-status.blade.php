@props(['invoice'])

@php
    $classes = [
        'unpaid'  => 'bg-rose-50 text-rose-700',
        'partial' => 'bg-amber-50 text-amber-700',
        'paid'    => 'bg-emerald-50 text-emerald-700',
    ][$invoice->status];
@endphp

<span {{ $attributes->merge(['class' => "text-xs font-bold rounded-full px-3 py-1 whitespace-nowrap $classes"]) }}>
    {{ $invoice->status_label }}
</span>