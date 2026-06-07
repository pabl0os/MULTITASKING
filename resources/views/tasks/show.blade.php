<x-layouts.app>
    <x-slot:header>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <a href="{{ $task->project_id ? route('projects.show', $task->project_id) : route('tasks') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $task->name }}</h1>
            </div>

            @if ($task->project_id && in_array($userRole, ['leader', 'coleader']))
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta tarea? Las que dependían de ella se desbloquearán.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center space-x-1.5 rounded-xl bg-red-50 border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span>Eliminar Tarea</span>
                    </button>
                </form>
            @elseif (!$task->project_id && $task->user_id === Auth::id())
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta tarea?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center space-x-1.5 rounded-xl bg-red-50 border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span>Eliminar Tarea</span>
                    </button>
                </form>
            @endif
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main: Task Details & Comments -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Details Card -->
            <div class="bg-white border border-slate-200 p-6 rounded-2xl">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Detalles de la Tarea</h3>
                <p class="text-sm text-slate-600 whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100 mb-6">
                    {{ $task->description ?? 'Sin descripción.' }}
                </p>

                <!-- Update Task Form -->
                <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="name" value="{{ $task->name }}" />
                    <input type="hidden" name="description" value="{{ $task->description }}" />
                    <input type="hidden" name="priority" value="{{ $task->priority }}" />
                    <input type="hidden" name="deadline" value="{{ $task->deadline ? $task->deadline->format('Y-m-d\TH:i') : '' }}" />
                    <input type="hidden" name="assignee_id" value="{{ $task->assignee_id }}" />

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">Estado Actual</label>
                            <select id="status" name="status" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white">
                                <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>En Proceso</option>
                                <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Realizada</option>
                                <option value="overdue" {{ $task->status === 'overdue' ? 'selected' : '' }}>Atrasada</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-hover">
                                Guardar Estado
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Comments Section -->
            <div class="bg-white border border-slate-200 p-6 rounded-2xl">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Comentarios</h3>
                
                <form action="{{ route('tasks.comments.add', $task) }}" method="POST" class="mb-6">
                    @csrf
                    <div>
                        <textarea name="content" required rows="3" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white placeholder:text-slate-400" placeholder="Escribe un comentario o avance..."></textarea>
                    </div>
                    <div class="flex justify-end mt-3">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-xs font-semibold text-white shadow-sm hover:bg-primary-hover">
                            Comentar
                        </button>
                    </div>
                </form>

                <div class="space-y-4">
                    @forelse ($comments as $comment)
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-700">{{ $comment->user->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-600">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No hay comentarios en esta tarea aún.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar: Meta Information & Dependencies -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 p-6 rounded-2xl">
                <h3 class="text-md font-bold text-slate-800 mb-4">Meta Información</h3>
                
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400">Proyecto:</span>
                        <span class="font-semibold text-slate-700">
                            {{ $task->project ? $task->project->name : 'Personal' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400">Creador:</span>
                        <span class="font-semibold text-slate-700">{{ $task->creator->name }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400">Asignada a:</span>
                        <span class="font-semibold text-slate-700">
                            {{ $task->assignee ? $task->assignee->name : 'Sin asignar' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400">Prioridad:</span>
                        <span class="font-semibold text-slate-700">
                            {{ ['Baja', 'Media-Baja', 'Media', 'Alta', 'Crítica'][$task->priority - 1] ?? 'Ninguna' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-400">Fecha Límite:</span>
                        <span class="font-semibold text-slate-700">
                            {{ $task->deadline ? $task->deadline->format('d/m/Y H:i') : 'Sin fecha' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Dependencies Card (Only if task belongs to a project) -->
            @if ($task->project_id)
                <div class="bg-white border border-slate-200 p-6 rounded-2xl">
                    <h3 class="text-md font-bold text-slate-800 mb-4">Serialización (Dependencias)</h3>
                    <p class="text-[11px] text-slate-400 mb-4">Esta tarea no podrá iniciarse hasta que todas las tareas predecesoras estén terminadas.</p>

                    <!-- Add Dependency form -->
                    @if (in_array($userRole, ['leader', 'coleader']))
                        <form action="{{ route('tasks.dependencies.add', $task) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex space-x-2">
                                <select name="depends_on_task_id" required class="flex-1 text-xs bg-slate-50 border border-slate-200 rounded-lg p-1.5 focus:outline-none">
                                    <option value="">Añadir predecesora...</option>
                                    @foreach ($availableDependencies as $availDep)
                                        <option value="{{ $availDep->id }}">{{ $availDep->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-3 py-1.5 bg-primary hover:bg-primary-hover text-white rounded-lg text-xs font-semibold">
                                    Añadir
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- Dependency List -->
                    <div class="space-y-2">
                        @forelse ($dependencies as $dep)
                            <div class="flex items-center justify-between bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-xs">
                                <div class="truncate flex-1 pr-2">
                                    <span class="font-medium text-slate-700 block truncate">{{ $dep->name }}</span>
                                    <span class="text-[10px] uppercase font-bold
                                        @if($dep->status === 'completed') text-green-500
                                        @else text-amber-500
                                        @endif">
                                        {{ $dep->status === 'completed' ? 'Completada' : 'Pendiente/En curso' }}
                                    </span>
                                </div>

                                @if (in_array($userRole, ['leader', 'coleader']))
                                    <form action="{{ route('tasks.dependencies.remove', [$task, $dep]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">Esta tarea no tiene dependencias.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
