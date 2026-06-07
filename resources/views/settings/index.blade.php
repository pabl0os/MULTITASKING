<x-layouts.app>
    <x-slot:header>
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Configuración</h1>
        </div>
        <p class="text-sm text-slate-500">Ajusta tus límites personales y preferencias de alertas.</p>
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
        <!-- Configuration Form (2 cols) -->
        <div class="lg:col-span-2 bg-white border border-slate-200 p-8 rounded-2xl">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Preferencias Personales</h3>

            <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- WIP Limit (M) -->
                <div>
                    <label for="max_in_process_tasks" class="block text-sm font-semibold text-slate-700 mb-2">Límite Personal WIP (M)</label>
                    <input type="number" id="max_in_process_tasks" name="max_in_process_tasks" value="{{ $user->max_in_process_tasks }}" min="1" max="100" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" />
                    <p class="text-xs text-slate-400 mt-2">Límite absoluto de tareas "En proceso" que puedes tener abiertas en toda la plataforma al mismo tiempo.</p>
                </div>

                <!-- Notifications -->
                <div class="border-t border-slate-100 pt-6">
                    <h4 class="text-sm font-bold text-slate-800 mb-4">Configuración de Alertas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="notify_sameday_hours" class="block text-sm font-semibold text-slate-700 mb-2">Notificar en el mismo día (Horas antes)</label>
                            <input type="number" id="notify_sameday_hours" name="notify_sameday_hours" value="{{ $user->notify_sameday_hours }}" min="1" max="24" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" />
                            <p class="text-[11px] text-slate-400 mt-1">Si la tarea se entrega hoy, recibirás la alerta estas horas antes del plazo.</p>
                        </div>

                        <div>
                            <label for="notify_diffday_days" class="block text-sm font-semibold text-slate-700 mb-2">Notificar para otros días (Días antes)</label>
                            <input type="number" id="notify_diffday_days" name="notify_diffday_days" value="{{ $user->notify_diffday_days }}" min="1" max="30" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-primary focus:bg-white" />
                            <p class="text-[11px] text-slate-400 mt-1">Si la tarea vence otro día, recibirás la alerta estos días antes del plazo.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-sm font-semibold text-white shadow-sm hover:bg-primary-hover">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        <!-- Danger Zone (1 col) -->
        <div class="bg-red-50/20 border border-red-200/50 p-6 rounded-2xl h-fit">
            <h3 class="text-md font-bold text-red-800 mb-4 flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span>Zona de Peligro</span>
            </h3>
            
            <p class="text-xs text-slate-500 mb-6">Si eliminas tu cuenta, se borrarán de forma definitiva todos tus datos personales, tareas individuales y registros de proyectos. Esta acción no se puede deshacer.</p>

            <form action="{{ route('account.delete') }}" method="POST" onsubmit="return confirm('¿Estás COMPLETAMENTE seguro de que deseas eliminar tu cuenta? Esta acción borrará todas tus tareas y reasignará o borrará tus proyectos.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                    Eliminar Cuenta
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
