<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Test lokalizacji</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        // Funkcja do wymuszenia pełnego odświeżenia strony
        function forceRefresh() {
            // Czyścimy cache przeglądarki dla tej strony
            window.location.reload(true);
        }
        
        // Sprawdzamy, czy URL zawiera parametr _nocache
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.href.includes('_nocache=')) {
                console.log('Wykryto parametr _nocache, odświeżam stronę...');
                // Usuwamy parametr _nocache z URL bez przeładowania strony
                const url = new URL(window.location.href);
                url.searchParams.delete('_nocache');
                window.history.replaceState({}, document.title, url.toString());
                
                // Wymuszamy pełne odświeżenie strony po 100 ms
                setTimeout(function() {
                    forceRefresh();
                }, 100);
            }
        });
    </script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6">Test lokalizacji - {{ app()->getLocale() }}</h1>
        
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-2">Przełączanie języka:</h2>
            <div class="flex space-x-4">
                <a href="{{ route('test.locale.set', 'pl') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Polski</a>
                <a href="{{ route('test.locale.set', 'en') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">English</a>
                <a href="{{ route('test.locale.set', 'de') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Deutsch</a>
            </div>
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-2">Testowe teksty:</h2>
            <p class="mb-2">Komunikat 1: {{ __('Welcome to InvestClub') }}</p>
            <p class="mb-2">Komunikat 2: {{ __('Elite community of investors') }}</p>
            <p class="mb-2">Komunikat 3: {{ __('For verified members only.') }}</p>
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-2">Ręczne przełączanie języka:</h2>
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 rounded-lg">
                <p class="text-yellow-800">
                    Uwaga: Po naciśnięciu poniższych przycisków strona zostanie odświeżona. Dodaliśmy automatyczne odświeżanie, żeby upewnić się, że zobaczysz tłumaczenia w wybranym języku.
                </p>
            </div>
            <form action="{{ route('language.switch', 'pl') }}" method="get" class="mb-2">
                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Zmień na Polski (przez trasę language.switch)
                </button>
            </form>
            <form action="{{ route('language.switch', 'en') }}" method="get" class="mb-2">
                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Zmień na English (przez trasę language.switch)
                </button>
            </form>
            <form action="{{ route('language.switch', 'de') }}" method="get" class="mb-2">
                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Zmień na Deutsch (przez trasę language.switch)
                </button>
            </form>
            
            <button onclick="forceRefresh()" class="mt-4 w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                Wymuś ręczne odświeżenie strony
            </button>
        </div>
        
        <div class="mt-8 border-t pt-4">
            <h2 class="text-xl font-bold mb-2">Informacje debugowania:</h2>
            <pre class="bg-gray-100 p-4 rounded-lg overflow-auto text-sm">{{ json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        
        <div class="mt-8">
            <a href="{{ route('welcome') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Powrót do strony głównej</a>
        </div>
    </div>
</body>
</html> 