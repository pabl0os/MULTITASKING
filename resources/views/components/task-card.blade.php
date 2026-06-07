@props([
    'task',
])

@php
    $title = $task->name;
    $projectName = $task->project ? $task->project->name : null;
    $dateStr = $task->deadline ? $task->deadline->format('d/m/Y H:i') : null;
    
    // Map Priority integer to string & badge color
    $priorityInt = $task->priority ?? 3;
    $priorityLabel = match($priorityInt) {
        1 => 'Baja',
        2 => 'Media-Baja',
        3 => 'Media',
        4 => 'Alta',
        5 => 'Crítica',
        default => 'Media'
    };
    $priorityColor = match($priorityInt) {
        4, 5 => 'danger',
        3 => 'warning',
        1, 2 => 'default',
        default => 'default',
    };

    // Map Status to Spanish label & badge color
    $statusLabel = match($task->status) {
        'pending' => 'Pendiente',
        'in_progress' => 'En Proceso',
        'completed' => 'Realizado',
        'overdue' => 'Atrasado',
        default => 'Pendiente'
    };
    $statusColor = match($task->status) {
        'in_progress' => 'primary',
        'completed' => 'success',
        'overdue' => 'danger',
        default => 'default',
    };

    $isCompleted = $task->status === 'completed';
    $isOverdue = $task->status === 'overdue';
@endphp

<div class="group relative flex flex-col xl:flex-row xl:items-center justify-between gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:shadow-md hover:border-slate-300 transition-all duration-200 mb-1">
    <div class="flex items-center space-x-3 min-w-0">
        <!-- Status Indicator Icon -->
        <div class="flex-shrink-0 w-5 h-5 rounded-full border flex items-center justify-center transition-colors
            {{ $isCompleted ? 'bg-green-500 border-green-500 text-white' : ($isOverdue ? 'bg-red-500 border-red-500 text-white' : 'border-slate-300 text-transparent') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>

        <!-- Task Info -->
        <div class="flex-1 min-w-0">
            <h4 class="text-sm font-semibold {{ $isCompleted ? 'text-slate-400 line-through' : 'text-slate-800 group-hover:text-primary transition-colors' }} truncate">
                {{ $title }}
            </h4>
            @if($projectName)
                <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $projectName }}</p>
            @endif
        </div>
    </div>

    <!-- Meta Info -->
    <div class="flex items-center space-x-2 xl:ml-4 flex-shrink-0 flex-wrap gap-y-2">
        @if($dateStr)
            <div class="flex items-center text-[11px] text-slate-400 space-x-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <span class="{{ $isOverdue ? 'text-red-500 font-medium' : '' }}">{{ $dateStr }}</span>
            </div>
        @endif
        
        <x-badge :type="$priorityColor">{{ $priorityLabel }}</x-badge>
        
        <x-badge :type="$statusColor">{{ $statusLabel }}</x-badge>
    </div>
</div>
