<aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-sidebar text-sidebar-text flex flex-col z-20 shadow-2xl transition-all duration-300">
    <!-- Collapse/Expand Toggle Button -->
    <button id="sidebar-toggle" class="absolute -right-3 top-7 w-6.5 h-6.5 bg-sidebar border border-slate-700/80 rounded-full flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors shadow-lg cursor-pointer z-30">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-300" id="toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>

    <!-- Logo -->
    <div class="h-20 flex items-center px-6 logo-wrapper transition-all duration-300">
        <div class="flex items-center space-x-3 logo-container">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary/20 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m13 2-2 2.5h3L11 22l2-2.5h-3L13 2z"/>
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-white sidebar-text-collapse">MultiTasking</span>
        </div>
    </div>

    <!-- User Profile Section -->
    @auth
        <div class="px-5 py-4 border-b border-slate-800/40 user-profile-container transition-all duration-300">
            <div class="flex items-center space-x-3 user-profile-wrapper">
                <!-- Avatar Circle -->
                <div class="w-9 h-9 rounded-xl bg-primary/20 border border-primary/30 flex-shrink-0 flex items-center justify-center font-bold text-primary text-sm shadow-inner shadow-primary/10 select-none">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <!-- User Info & Logout -->
                <div class="flex-1 min-w-0 flex items-center justify-between sidebar-text-collapse">
                    <div class="min-w-0 pr-2">
                        <h4 class="text-xs font-semibold text-slate-200 truncate" title="{{ auth()->user()->name }}">
                            {{ auth()->user()->name }}
                        </h4>
                        <span class="block text-[10px] text-slate-500 truncate mt-0.5">{{ auth()->user()->email }}</span>
                    </div>
                    <!-- Logout Action -->
                    <form action="{{ route('logout') }}" method="POST" class="inline flex-shrink-0">
                        @csrf
                        <button type="submit" class="text-slate-500 hover:text-red-400 p-1.5 rounded-lg hover:bg-slate-800/50 transition-colors cursor-pointer" title="Cerrar sesión">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endauth


    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1">
        @php
            $navItems = [
                ['name' => 'Cerebro', 'url' => '/dashboard', 'icon' => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>'],
                ['name' => 'Proyectos', 'url' => '/projects', 'icon' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>'],
                ['name' => 'Tareas', 'url' => '/tasks', 'icon' => '<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/>'],
                ['name' => 'Notificaciones', 'url' => '/notifications', 'icon' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>'],
            ];
            $currentPath = request()->path();
        @endphp

        @foreach ($navItems as $item)
            @php
                $isActive = str_starts_with($currentPath, ltrim($item['url'], '/')) || ($currentPath == '/' && $item['url'] == '/tasks');
            @endphp
            <a href="{{ $item['url'] }}" 
               class="flex items-center space-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all {{ $isActive ? 'bg-sidebar-hover text-sidebar-active shadow-sm shadow-black/10 relative' : 'text-sidebar-text hover:bg-sidebar-hover/50 hover:text-slate-200' }}">
               
               @if($isActive)
               <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-primary rounded-r-full"></div>
               @endif

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 opacity-80 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    {!! $item['icon'] !!}
                </svg>
                <span class="sidebar-text-collapse">{{ $item['name'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- Bottom Actions -->
    <div class="p-4 border-t border-slate-800/50 bottom-actions-container transition-all duration-300">
        <a href="/settings" class="flex items-center space-x-3 rounded-xl px-3 py-2.5 text-sm font-medium text-sidebar-text transition-all hover:bg-sidebar-hover/50 hover:text-slate-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 opacity-80 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            <span class="sidebar-text-collapse">Configuración</span>
        </a>
    </div>
</aside>
