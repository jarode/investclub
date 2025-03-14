<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Managera') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">Witaj, {{ Auth::user()->name }}</h1>
                <p class="mb-4">To jest panel managera dostępny tylko dla użytkowników z rolą "manager".</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    <div class="bg-gray-100 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-3">Zarządzanie użytkownikami</h3>
                        <p class="mb-4">Zarządzanie standardowymi użytkownikami platformy.</p>
                        <a href="{{ route('users.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Lista użytkowników
                        </a>
                    </div>
                    
                    <div class="bg-gray-100 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-3">Statystyki</h3>
                        <ul class="list-disc list-inside mb-4">
                            <li>Liczba inwestorów: {{ \App\Models\User::where('role', 'investor')->count() }}</li>
                            <li>Liczba zweryfikowanych użytkowników: {{ \App\Models\User::where('verification_status', 'verified')->count() }}</li>
                            <li>Liczba oczekujących na weryfikację: {{ \App\Models\User::where('verification_status', 'pending')->count() }}</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-6">
                    <p class="mb-4 text-red-600">Uwaga: Jako manager nie możesz modyfikować kont administratorów.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
