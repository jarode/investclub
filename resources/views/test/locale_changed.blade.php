<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Zmieniono język na {{ $locale }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6">Język został zmieniony na: {{ $locale }}</h1>
        
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-2">Testowe teksty w nowym języku:</h2>
            <p class="mb-2">Komunikat 1: {{ __('Welcome to InvestClub') }}</p>
            <p class="mb-2">Komunikat 2: {{ __('Elite community of investors') }}</p>
            <p class="mb-2">Komunikat 3: {{ __('For verified members only.') }}</p>
        </div>
        
        <div class="mb-8 p-4 bg-green-100 border border-green-400 rounded-lg">
            <p class="text-green-800">
                Język został poprawnie zmieniony na <strong>{{ $locale }}</strong>. 
                Gdy wrócisz do poprzedniej strony lub strony głównej, język powinien być zmieniony.
            </p>
        </div>
        
        <div class="mt-8 border-t pt-4">
            <h2 class="text-xl font-bold mb-2">Informacje debugowania:</h2>
            <pre class="bg-gray-100 p-4 rounded-lg overflow-auto text-sm">{{ json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        
        <div class="mt-8 flex space-x-4">
            <a href="{{ route('test.locale') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Powrót do testu lokalizacji</a>
            <a href="{{ route('welcome') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Powrót do strony głównej</a>
        </div>
    </div>
</body>
</html> 