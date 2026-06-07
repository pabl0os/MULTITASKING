<x-layouts.app>
    <x-slot:header>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <a href="{{ route('projects') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </a>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $project->name }}</h1>
                    <x-badge type="{{ $project->status === 'completed' ? 'success' : 'info' }}">
                        {{ $project->status === 'completed' ? 'Completado' : 'Activo' }}
                    </x-badge>
                </div>
                <p class="text-sm text-slate-500 max-w-2xl">{{ $project->description }}</p>
            </div>

            <div class="flex items-center space-x-3">
                @if ($userRole === 'leader' && $project->status !== 'completed')
                    <form action="{{ route('projects.complete', $project) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 rounded-xl bg-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Completar Proyecto</span>
                        </button>
                    </form>
                @endif

                @if (in_array($userRole, ['leader', 'coleader']))
                    <button onclick="document.getElementById('edit-project-modal').classList.remove('hidden')" class="flex items-center space-x-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition-all hover:bg-slate-50 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        <span>Editar</span>
                    </button>
                @endif

                @if ($userRole === 'leader')
                    <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este proyecto por completo? Esta acción es irreversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center space-x-2 rounded-xl bg-red-50 border border-red-200 px-5 py-2.5 text-sm font-bold text-red-600 shadow-sm transition-all hover:bg-red-100 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            <span>Eliminar</span>
                        </button>
                    </form>
                @endif
            </div>
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

    <!-- Project Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-slate-200 p-5 rounded-2xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Límite WIP (N)</span>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $project->max_in_process_per_user }}</div>
            <p class="text-xs text-slate-400 mt-1">Límite de tareas "En proceso"</p>
        </div>
        <div class="bg-white border border-slate-200 p-5 rounded-2xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Fecha Límite</span>
            <div class="text-2xl font-bold text-slate-800 mt-1">
                {{ $project->global_deadline ? $project->global_deadline->format('d/m/Y') : 'Sin fecha' }}
            </div>
            <p class="text-xs text-slate-400 mt-1">Plazo global del proyecto</p>
        </div>
        <div class="bg-white border border-slate-200 p-5 rounded-2xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Prioridad</span>
            <div class="text-2xl font-bold text-slate-800 mt-1">
                @if($project->global_priority)
                    {{ ['Baja', 'Media-Baja', 'Media', 'Alta', 'Crítica'][$project->global_priority - 1] ?? 'Ninguna' }}
                @else
                    Ninguna
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-1">Urgencia general</p>
        </div>
        <div class="bg-white border border-slate-200 p-5 rounded-2xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Progreso</span>
            <div class="flex items-center space-x-2 mt-1">
                <div class="text-2xl font-bold text-slate-800">{{ $progress }}%</div>
                <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-primary h-2 rounded-full" style="width: {{ $progress }}%"></div>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-1">Tareas realizadas</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Area: Task Board (3 cols) -->
        <div class="lg:col-span-3 space-y-8">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-800">Tareas del Proyecto</h2>
                @if (in_array($userRole, ['leader', 'coleader']) && $project->status !== 'completed')
                    <button onclick="document.getElementById('new-task-modal').classList.remove('hidden')" class="flex items-center space-x-1.5 text-sm font-semibold text-primary hover:text-primary-hover">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Crear Tarea</span>
                    </button>
                @endif
            </div>

            <!-- Kanban Board Columns -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Pendiente -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/50 flex flex-col min-h-[300px]">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-slate-400 rounded-full"></span>
                            <h3 class="font-bold text-slate-700 text-sm">Pendientes</h3>
                        </div>
                        <span class="text-xs font-bold text-slate-400 bg-white px-2 py-0.5 rounded-full border border-slate-100">
                            {{ $tasks->where('status', 'pending')->count() }}
                        </span>
                    </div>
                    <div class="space-y-4 flex-1">
                        @forelse ($tasks->where('status', 'pending') as $task)
                            <a href="{{ route('tasks.show', $task) }}" class="block">
                                <x-task-card :task="$task" />
                            </a>
                        @empty
                            <div class="text-center py-8 text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">No hay tareas pendientes.</div>
                        @endforelse
                    </div>
                </div>

                <!-- En Proceso -->
                <div class="bg-blue-50/30 p-4 rounded-2xl border border-blue-100/50 flex flex-col min-h-[300px]">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                            <h3 class="font-bold text-slate-700 text-sm">En Proceso</h3>
                        </div>
                        <span class="text-xs font-bold text-blue-500 bg-white px-2 py-0.5 rounded-full border border-blue-100">
                            {{ $tasks->where('status', 'in_progress')->count() }}
                        </span>
                    </div>
                    <div class="space-y-4 flex-1">
                        @forelse ($tasks->where('status', 'in_progress') as $task)
                            <a href="{{ route('tasks.show', $task) }}" class="block">
                                <x-task-card :task="$task" />
                            </a>
                        @empty
                            <div class="text-center py-8 text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">No hay tareas en proceso.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Realizado -->
                <div class="bg-green-50/30 p-4 rounded-2xl border border-green-100/50 flex flex-col min-h-[300px]">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                            <h3 class="font-bold text-slate-700 text-sm">Realizadas</h3>
                        </div>
                        <span class="text-xs font-bold text-green-500 bg-white px-2 py-0.5 rounded-full border border-green-100">
                            {{ $tasks->where('status', 'completed')->count() }}
                        </span>
                    </div>
                    <div class="space-y-4 flex-1">
                        @forelse ($tasks->where('status', 'completed') as $task)
                            <a href="{{ route('tasks.show', $task) }}" class="block">
                                <x-task-card :task="$task" />
                            </a>
                        @empty
                            <div class="text-center py-8 text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">No hay tareas completadas.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- History log -->
            <div class="bg-white border border-slate-200 p-6 rounded-2xl">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Registro de Historial</h3>
                <div class="flow-root max-h-[300px] overflow-y-auto pr-2">
                    <ul role="list" class="-mb-8">
                        @forelse ($histories as $history)
                            <li>
                                <div class="relative pb-8">
                                    @if (!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0 pt-1.5 flex flex-col xl:flex-row xl:justify-between xl:space-x-4">
                                            <div class="min-w-0 mb-1 xl:mb-0">
                                                <p class="text-sm text-slate-600 break-words">
                                                    <strong>{{ $history->user->name }}</strong> 
                                                    @if($history->action === 'project_updated')
                                                        actualizó la configuración del proyecto
                                                    @elseif($history->action === 'project_completed')
                                                        completó el proyecto
                                                    @elseif($history->action === 'task_created')
                                                        creó la tarea <strong>{{ $history->task->name ?? 'Eliminada' }}</strong>
                                                    @elseif($history->action === 'task_updated')
                                                        actualizó la tarea <strong>{{ $history->task->name ?? 'Eliminada' }}</strong>
                                                    @elseif($history->action === 'task_completed')
                                                        completó la tarea <strong>{{ $history->task->name ?? 'Eliminada' }}</strong>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right text-xs whitespace-nowrap text-slate-400">
                                                <time datetime="{{ $history->created_at }}">{{ $history->created_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p class="text-sm text-slate-400">No hay registros en el historial de este proyecto.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sidebar: Members Management (1 col) -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Miembros</h3>
                    @if (in_array($userRole, ['leader', 'coleader']) && $project->status !== 'completed')
                        <button onclick="document.getElementById('invite-member-modal').classList.remove('hidden')" class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center space-x-1">
                            <span>+ Invitar</span>
                        </button>
                    @endif
                </div>

                <div class="space-y-4">
                    @foreach ($members as $member)
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex-shrink-0 flex items-center justify-center font-bold text-slate-600 text-xs border border-slate-200">
                                    {{ substr($member->name, 0, 2) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-semibold text-slate-800 truncate" title="{{ $member->name }}">{{ $member->name }}</h4>
                                    <span class="block truncate text-[10px] font-bold uppercase tracking-wider
                                        @if($member->pivot->role === 'leader') text-red-500
                                        @elseif($member->pivot->role === 'coleader') text-blue-500
                                        @else text-slate-500
                                        @endif">
                                        {{ $member->pivot->role === 'leader' ? 'Líder' : ($member->pivot->role === 'coleader' ? 'Colíder' : 'Miembro') }}
                                    </span>
                                </div>
                            </div>

                            @if ($project->status !== 'completed')
                                <!-- Member Roles Actions -->
                                <div class="flex items-center space-x-1 flex-shrink-0">
                                    @if ($userRole === 'leader' && $member->id !== Auth::id())
                                        <!-- Promote/Demote dropdown/action for leader -->
                                        <form action="{{ route('projects.members.role', [$project, $member]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <select onchange="this.form.submit()" name="role" class="text-[11px] bg-slate-50 border border-slate-200 rounded px-1.5 py-0.5 text-slate-600 focus:outline-none focus:ring-1 focus:ring-primary">
                                                <option value="member" {{ $member->pivot->role === 'member' ? 'selected' : '' }}>Miembro</option>
                                                <option value="coleader" {{ $member->pivot->role === 'coleader' ? 'selected' : '' }}>Colíder</option>
                                                <option value="leader">Hacer Líder</option>
                                            </select>
                                        </form>
                                    @endif

                                    <!-- Remove button -->
                                    @if (
                                        (Auth::id() !== $member->id) && 
                                        ($userRole === 'leader' || ($userRole === 'coleader' && $member->pivot->role === 'member'))
                                    )
                                        <form action="{{ route('projects.members.remove', [$project, $member]) }}" method="POST" onsubmit="return confirm('¿Remover a este miembro? Sus tareas quedarán sin asignar.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-500 p-1 rounded hover:bg-slate-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Proyecto -->
    @if (in_array($userRole, ['leader', 'coleader']))
        <div id="edit-project-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xl w-full max-w-lg p-8 mx-4 relative">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-slate-900">Editar Proyecto</h3>
                    <button onclick="document.getElementById('edit-project-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="edit_name" class="block text-sm font-semibold text-slate-700 mb-2">Nombre del Proyecto</label>
                        <input type="text" id="edit_name" name="name" value="{{ $project->name }}" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" />
                    </div>

                    <div>
                        <label for="edit_description" class="block text-sm font-semibold text-slate-700 mb-2">Descripción</label>
                        <textarea id="edit_description" name="description" rows="3" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white">{{ $project->description }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit_global_priority" class="block text-sm font-semibold text-slate-700 mb-2">Prioridad</label>
                            <select id="edit_global_priority" name="global_priority" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white">
                                <option value="">Ninguna</option>
                                <option value="1" {{ $project->global_priority === 1 ? 'selected' : '' }}>Baja</option>
                                <option value="2" {{ $project->global_priority === 2 ? 'selected' : '' }}>Media-Baja</option>
                                <option value="3" {{ $project->global_priority === 3 ? 'selected' : '' }}>Media</option>
                                <option value="4" {{ $project->global_priority === 4 ? 'selected' : '' }}>Alta</option>
                                <option value="5" {{ $project->global_priority === 5 ? 'selected' : '' }}>Crítica</option>
                            </select>
                        </div>

                        <div>
                            <label for="edit_global_deadline" class="block text-sm font-semibold text-slate-700 mb-2">Fecha Límite</label>
                            <input type="date" id="edit_global_deadline" name="global_deadline" value="{{ $project->global_deadline ? $project->global_deadline->format('Y-m-d') : '' }}" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" />
                        </div>
                    </div>

                    <div>
                        <label for="edit_max_in_process_per_user" class="block text-sm font-semibold text-slate-700 mb-2">Límite WIP de tareas (N)</label>
                        <input type="number" id="edit_max_in_process_per_user" name="max_in_process_per_user" value="{{ $project->max_in_process_per_user }}" min="1" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" {{ $userRole !== 'leader' ? 'disabled' : '' }} />
                        @if($userRole !== 'leader')
                            <p class="text-xs text-red-500 mt-1">Solo el líder puede modificar este límite.</p>
                        @endif
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="document.getElementById('edit-project-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-sm font-semibold text-white shadow-sm hover:bg-primary-hover">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Invitar Miembro -->
    @if (in_array($userRole, ['leader', 'coleader']))
        <div id="invite-member-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xl w-full max-w-md p-8 mx-4 relative">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-slate-900">Agregar Miembro</h3>
                    <button onclick="document.getElementById('invite-member-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form action="{{ route('projects.members.add', $project) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="invite_user_id" class="block text-sm font-semibold text-slate-700 mb-2">Usuario</label>
                        <select id="invite_user_id" name="user_id" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white">
                            <option value="">Selecciona un usuario</option>
                            @foreach ($availableUsers as $avail)
                                <option value="{{ $avail->id }}">{{ $avail->name }} ({{ $avail->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="invite_role" class="block text-sm font-semibold text-slate-700 mb-2">Rol inicial</label>
                        <select id="invite_role" name="role" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white">
                            <option value="member">Miembro</option>
                            @if ($userRole === 'leader')
                                <option value="coleader">Colíder</option>
                            @endif
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="document.getElementById('invite-member-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-sm font-semibold text-white shadow-sm hover:bg-primary-hover">
                            Agregar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Nueva Tarea (Integración CRUD Tareas) -->
    @if (in_array($userRole, ['leader', 'coleader']))
        <div id="new-task-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xl w-full max-w-lg p-8 mx-4 relative">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-slate-900">Crear Tarea</h3>
                    <button onclick="document.getElementById('new-task-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}" />
                    
                    <div>
                        <label for="task_name" class="block text-sm font-semibold text-slate-700 mb-2">Nombre de la Tarea</label>
                        <input type="text" id="task_name" name="name" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" placeholder="Ej. Investigar mercado objetivo" />
                    </div>

                    <div>
                        <label for="task_description" class="block text-sm font-semibold text-slate-700 mb-2">Descripción</label>
                        <textarea id="task_description" name="description" rows="3" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" placeholder="Detalles de lo que se debe hacer"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="task_priority" class="block text-sm font-semibold text-slate-700 mb-2">Prioridad</label>
                            <select id="task_priority" name="priority" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white">
                                <option value="1">Baja</option>
                                <option value="2">Media-Baja</option>
                                <option value="3" selected>Media</option>
                                <option value="4">Alta</option>
                                <option value="5">Crítica</option>
                            </select>
                        </div>

                        <div>
                            <label for="task_deadline" class="block text-sm font-semibold text-slate-700 mb-2">Fecha y Hora Límite</label>
                            <input type="datetime-local" id="task_deadline" name="deadline" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" />
                        </div>
                    </div>

                    <div>
                        <label for="task_assignee_id" class="block text-sm font-semibold text-slate-700 mb-2">Asignar a</label>
                        <select id="task_assignee_id" name="assignee_id" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white">
                            <option value="">Sin asignar (Cualquiera)</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="document.getElementById('new-task-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-sm font-semibold text-white shadow-sm hover:bg-primary-hover">
                            Crear Tarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-layouts.app>
