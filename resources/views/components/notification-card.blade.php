@props([
    'message',
    'time',
    'type' => 'info', // info, alert, comment, assignment
])

@php
    $iconConfig = match($type) {
        'alert' => ['color' => 'text-red-500', 'bg' => 'bg-red-50', 'icon' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>'],
        'comment' => ['color' => 'text-blue-500', 'bg' => 'bg-blue-50', 'icon' => '<path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/>'],
        'assignment' => ['color' => 'text-yellow-500', 'bg' => 'bg-yellow-50', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/>'],
        default => ['color' => 'text-slate-500', 'bg' => 'bg-slate-50', 'icon' => '<circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>'],
    };
@endphp

<div class="flex items-start p-4 bg-white border border-slate-200 rounded-xl hover:shadow-sm transition-all mb-3">
    <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $iconConfig['bg'] }} flex items-center justify-center {{ $iconConfig['color'] }} mr-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            {!! $iconConfig['icon'] !!}
        </svg>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-slate-800">
            {!! $message !!}
        </p>
        <p class="text-xs text-slate-500 mt-1">{{ $time }}</p>
    </div>
</div>
