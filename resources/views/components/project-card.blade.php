@props([
    'title',
    'description',
    'progress' => 0,
    'membersCount' => 1,
    'tasksCount' => 0,
])

<div class="bg-white border border-slate-200 p-5 rounded-2xl hover:shadow-lg hover:border-slate-300 transition-all duration-300 group cursor-pointer flex flex-col h-full">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800 group-hover:text-primary transition-colors">{{ $title }}</h3>
            <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $description }}</p>
        </div>
        
        <div class="flex items-center space-x-1 text-slate-400 text-xs font-medium bg-slate-50 px-2 py-1 rounded-md border border-slate-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>{{ $membersCount }}</span>
            <span class="px-1">•</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
            <span>{{ $tasksCount }}</span>
        </div>
    </div>
    
    <div class="mt-auto">
        <div class="flex justify-between items-end mb-2">
            <span class="text-xs font-semibold text-slate-600">Progreso</span>
            <span class="text-xs font-bold text-primary">{{ $progress }}%</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
            <div class="bg-primary h-2 rounded-full transition-all duration-1000 ease-out" style="width: {{ $progress }}%"></div>
        </div>
    </div>
</div>
