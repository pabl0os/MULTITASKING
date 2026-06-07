@props(['type' => 'default'])

@php
    $classes = match($type) {
        'success' => 'bg-green-100 text-green-700 border-green-200',
        'danger' => 'bg-red-100 text-red-700 border-red-200',
        'warning' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'primary' => 'bg-blue-100 text-blue-700 border-blue-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200'
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border $classes transition-colors"]) }}>
    {{ $slot }}
</span>
