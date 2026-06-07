<x-layouts.app>
    <x-slot:header>
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tareas</h1>
                </div>
                <p class="text-sm text-slate-500">Todas tus tareas personales y de proyectos.</p>
            </div>
            
            <button onclick="document.getElementById('new-personal-task-modal').classList.remove('hidden')" class="flex items-center space-x-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-primary-hover hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Nueva tarea</span>
            </button>
        </div>
    </x-slot:header>

    @if (session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filters & Sorting Controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <!-- Status Tabs -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-2 md:pb-0">
            <div class="p-1 mr-2 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            </div>
            
            @php $currentStatus = request('status', 'all'); @endphp
            <a href="{{ route('tasks', ['status' => 'all', 'sort' => request('sort')]) }}" 
               class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $currentStatus === 'all' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }} transition-colors">Todos</a>
            
            <a href="{{ route('tasks', ['status' => 'pending', 'sort' => request('sort')]) }}" 
               class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $currentStatus === 'pending' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }} transition-colors">Pendientes</a>
            
            <a href="{{ route('tasks', ['status' => 'in_progress', 'sort' => request('sort')]) }}" 
               class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $currentStatus === 'in_progress' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }} transition-colors">En progreso</a>
            
            <a href="{{ route('tasks', ['status' => 'completed', 'sort' => request('sort')]) }}" 
               class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $currentStatus === 'completed' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }} transition-colors">Realizadas</a>

            <a href="{{ route('tasks', ['status' => 'overdue', 'sort' => request('sort')]) }}" 
               class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $currentStatus === 'overdue' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }} transition-colors">Atrasadas</a>
        </div>

        <!-- Sort Select -->
        <div class="flex items-center space-x-2">
            <span class="text-xs font-semibold text-slate-400">Ordenar por:</span>
            @php $currentSort = request('sort', 'recommended'); @endphp
            <div class="inline-flex rounded-xl border border-slate-200 bg-white p-0.5">
                <a href="{{ route('tasks', ['status' => request('status'), 'sort' => 'recommended']) }}" 
                   class="px-3 py-1 rounded-lg text-xs font-bold {{ $currentSort === 'recommended' ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">Recomendado</a>
                <a href="{{ route('tasks', ['status' => request('status'), 'sort' => 'priority']) }}" 
                   class="px-3 py-1 rounded-lg text-xs font-bold {{ $currentSort === 'priority' ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">Prioridad</a>
                <a href="{{ route('tasks', ['status' => request('status'), 'sort' => 'date']) }}" 
                   class="px-3 py-1 rounded-lg text-xs font-bold {{ $currentSort === 'date' ? 'bg-slate-100 text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">Fecha</a>
            </div>
        </div>
    </div>

    <!-- Task List -->
    <div class="space-y-3">
        @forelse ($tasks as $task)
            <a href="{{ route('tasks.show', $task) }}" class="block">
                <x-task-card :task="$task" />
            </a>
        @empty
            <div class="text-center py-12 bg-white rounded-2xl border border-slate-200">
                <p class="text-slate-500">No tienes tareas en esta sección.</p>
                <button onclick="document.getElementById('new-personal-task-modal').classList.remove('hidden')" class="mt-4 text-sm font-semibold text-primary hover:text-primary-hover">
                    Crea una tarea ahora
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Nueva Tarea Personal -->
    <div id="new-personal-task-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xl w-full max-w-lg p-8 mx-4 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-900">Nueva Tarea</h3>
                <button onclick="document.getElementById('new-personal-task-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nombre de la Tarea</label>
                    <input type="text" id="name" name="name" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" placeholder="Ej. Comprar viveres" />
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Descripción</label>
                    <textarea id="description" name="description" rows="3" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" placeholder="Detalles de la tarea..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="priority" class="block text-sm font-semibold text-slate-700 mb-2">Prioridad</label>
                        <select id="priority" name="priority" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white">
                            <option value="1">Baja</option>
                            <option value="2">Media-Baja</option>
                            <option value="3" selected>Media</option>
                            <option value="4">Alta</option>
                            <option value="5">Crítica</option>
                        </select>
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-semibold text-slate-700 mb-2">Fecha y Hora Límite</label>
                        <input type="datetime-local" id="deadline" name="deadline" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" />
                    </div>
                </div>

                <div>
                    <label for="project_id" class="block text-sm font-semibold text-slate-700 mb-2">Proyecto (Opcional)</label>
                    <select id="project_id" name="project_id" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white">
                        <option value="">Personal (Ninguno)</option>
                        @foreach ($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="document.getElementById('new-personal-task-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-sm font-semibold text-white shadow-sm hover:bg-primary-hover">
                        Crear Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
