<x-layouts.app>
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Proyectos</h1>
                </div>
                <p class="text-sm text-slate-500">Gestiona tus proyectos colaborativos.</p>
            </div>
            
            <button onclick="document.getElementById('new-project-modal').classList.remove('hidden')" class="flex items-center space-x-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-primary-hover hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Nuevo proyecto</span>
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($projects as $project)
            <a href="{{ route('projects.show', $project) }}" class="block h-full">
                <x-project-card 
                    :title="$project->name"
                    :description="$project->description"
                    :progress="$project->progress"
                    :membersCount="$project->users_count"
                    :tasksCount="$project->tasks_count"
                />
            </a>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-slate-200">
                <p class="text-slate-500">No tienes proyectos creados o asignados.</p>
                <button onclick="document.getElementById('new-project-modal').classList.remove('hidden')" class="mt-4 text-sm font-semibold text-primary hover:text-primary-hover">
                    Crea tu primer proyecto
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Nuevo Proyecto -->
    <div id="new-project-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xl w-full max-w-lg p-8 mx-4 overflow-hidden relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-900">Nuevo Proyecto</h3>
                <button onclick="document.getElementById('new-project-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nombre del Proyecto</label>
                    <input type="text" id="name" name="name" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 placeholder:text-slate-400" placeholder="Ej. Rediseño App Móvil" />
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Descripción</label>
                    <textarea id="description" name="description" rows="3" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 placeholder:text-slate-400" placeholder="¿De qué trata este proyecto?"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="global_priority" class="block text-sm font-semibold text-slate-700 mb-2">Prioridad (Opcional)</label>
                        <select id="global_priority" name="global_priority" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Ninguna</option>
                            <option value="1">Baja</option>
                            <option value="2">Media-Baja</option>
                            <option value="3">Media</option>
                            <option value="4">Alta</option>
                            <option value="5">Crítica</option>
                        </select>
                    </div>

                    <div>
                        <label for="global_deadline" class="block text-sm font-semibold text-slate-700 mb-2">Fecha Límite (Opcional)</label>
                        <input type="date" id="global_deadline" name="global_deadline" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    </div>
                </div>

                <div>
                    <label for="max_in_process_per_user" class="block text-sm font-semibold text-slate-700 mb-2">Límite WIP de tareas (N)</label>
                    <input type="number" id="max_in_process_per_user" name="max_in_process_per_user" value="3" min="1" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    <p class="text-xs text-slate-400 mt-1">Límite máximo de tareas "En proceso" que un miembro puede tener activas a la vez en este proyecto.</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="document.getElementById('new-project-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-sm font-semibold text-white shadow-sm hover:bg-primary-hover transition-colors">
                        Crear Proyecto
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
