<x-layouts.app>
    <x-slot:header>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Notificaciones</h1>
            </div>

            @if ($notifications->whereNull('read_at')->isNotEmpty())
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-primary hover:text-primary-hover bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl">
                        Marcar todas como leídas
                    </button>
                </form>
            @endif
        </div>
        <p class="text-sm text-slate-500">Recordatorios y actividad reciente de tus tareas.</p>
    </x-slot:header>

    @if (session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            @php
                $data = $notification->data;
                $type = $data['type'] ?? 'info';
                $message = $data['message'] ?? 'Alerta de sistema.';
                $isRead = !is_null($notification->read_at);

                $iconConfig = match($type) {
                    'alert', 'task_overdue' => ['color' => 'text-red-500', 'bg' => 'bg-red-50', 'icon' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>'],
                    'comment' => ['color' => 'text-blue-500', 'bg' => 'bg-blue-50', 'icon' => '<path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/>'],
                    'assignment', 'task_unlocked', 'task_warning' => ['color' => 'text-yellow-500', 'bg' => 'bg-yellow-50', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/>'],
                    default => ['color' => 'text-slate-500', 'bg' => 'bg-slate-50', 'icon' => '<circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>'],
                };
            @endphp
            
            <div class="flex items-center justify-between bg-white border border-slate-200 p-4 rounded-xl hover:shadow-sm transition-all {{ $isRead ? 'opacity-60' : 'border-l-4 border-l-primary' }} mb-3">
                <div class="flex items-start flex-1 min-w-0 mr-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $iconConfig['bg'] }} flex items-center justify-center {{ $iconConfig['color'] }} mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            {!! $iconConfig['icon'] !!}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800">
                            {!! $message !!}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 flex-shrink-0">
                    @if (!$isRead)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-primary bg-slate-50 border border-slate-200 px-2 py-1 rounded-lg">
                                Marcar leída
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-slate-400 hover:text-red-500 p-1 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-2xl border border-slate-200">
                <p class="text-slate-500">No tienes notificaciones pendientes.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
