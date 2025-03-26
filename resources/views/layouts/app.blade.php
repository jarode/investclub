<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Dodatkowe skrypty -->
        <script>
            // Ustawienia globalne dla AJAX
            window.csrfToken = '{{ csrf_token() }}';
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <!-- Skrypt do obsługi zmiany języka -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Sprawdzamy, czy URL zawiera parametr _nocache (oznacza to zmianę języka)
                if (window.location.href.includes('_nocache=')) {
                    console.log('Wykryto zmianę języka, odświeżam stronę...');
                    
                    // Usuwamy parametr _nocache z URL bez przeładowania strony
                    const url = new URL(window.location.href);
                    url.searchParams.delete('_nocache');
                    window.history.replaceState({}, document.title, url.toString());
                    
                    // Wymuszamy pełne odświeżenie strony po małym opóźnieniu
                    setTimeout(function() {
                        window.location.reload(true);
                    }, 100);
                }
            });
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <x-banner />

            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        
        @stack('scripts')
    </body>
</html>
