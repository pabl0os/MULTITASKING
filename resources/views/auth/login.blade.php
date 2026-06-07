<x-layouts.guest>
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Iniciar Sesión</h1>
        <p class="text-sm text-slate-500 mt-2">Ingresa a tu espacio de trabajo</p>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/login" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
            <input id="email" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 transition-colors focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 placeholder:text-slate-400" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="tu@email.com" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Contraseña</label>
            <input id="password" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 transition-colors focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 placeholder:text-slate-400" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-primary-hover hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                Iniciar Sesión
            </button>
        </div>
        
        <div class="text-center mt-6">
            <a href="/register" class="text-sm font-medium text-slate-500 hover:text-primary transition-colors">
                ¿No tienes cuenta? Regístrate
            </a>
        </div>
    </form>
</x-layouts.guest>
