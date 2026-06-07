<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MultiTasking') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-bg-light">
        <script>
            (function () {
                if (localStorage.getItem('sidebar-collapsed') === 'true') {
                    document.body.classList.add('sidebar-collapsed');
                }
            })();
        </script>
        <div class="min-h-screen flex w-full">
            <!-- Sidebar Navigation -->
            <x-sidebar />

            <!-- Main Content Area -->
            <main id="main-content" class="flex-1 ml-64 p-8 overflow-y-auto h-screen">
                <div class="max-w-6xl mx-auto">
                    <!-- Header/Title section typically injected here if needed, or included in the view -->
                    @if (isset($header))
                        <header class="mb-8">
                            {{ $header }}
                        </header>
                    @endif

                    <!-- Page Content -->
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
