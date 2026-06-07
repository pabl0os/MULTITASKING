<x-layouts.app>
    <x-slot:header>
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Cerebro</h1>
        </div>
        <p class="text-sm text-slate-500">Tu vista global de prioridades — todo en un solo lugar.</p>
    </x-slot:header>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-center">
            <div class="flex items-center space-x-2 text-slate-500 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span class="text-sm font-medium">Pendientes</span>
            </div>
            <span class="text-3xl font-bold text-slate-800">{{ $pendingCount }}</span>
        </div>
        
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-center">
            <div class="flex items-center space-x-2 text-primary mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                <span class="text-sm font-medium">En progreso</span>
            </div>
            <span class="text-3xl font-bold text-slate-800">{{ $inProgressCount }}</span>
        </div>
        
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-center">
            <div class="flex items-center space-x-2 text-green-600 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span class="text-sm font-medium">Terminadas hoy</span>
            </div>
            <span class="text-3xl font-bold text-slate-800">{{ $completedTodayCount }}</span>
        </div>
    </div>

    <!-- Prioritized Tasks -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tareas Priorizadas (Recomendadas)</h2>
            <a href="{{ route('tasks') }}" class="text-xs font-semibold text-primary hover:text-primary-hover">Ver todas las tareas</a>
        </div>
        <div class="space-y-3">
            @forelse ($prioritizedTasks as $task)
                <a href="{{ route('tasks.show', $task) }}" class="block">
                    <x-task-card :task="$task" />
                </a>
            @empty
                <div class="text-center py-12 bg-white rounded-2xl border border-slate-200">
                    <p class="text-slate-500">No tienes tareas pendientes o en proceso.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
