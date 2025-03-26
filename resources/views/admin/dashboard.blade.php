<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Administratora') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">{{ __('Zarządzanie systemem') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Zarządzanie użytkownikami -->
                    <a href="{{ route('users.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300">
                        <h4 class="font-semibold mb-2">{{ __('Użytkownicy') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Zarządzaj użytkownikami platformy.') }}</p>
                    </a>
                    
                    <!-- Zarządzanie projektami -->
                    <a href="{{ route('projects.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300">
                        <h4 class="font-semibold mb-2">{{ __('Projekty') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Przeglądaj i zarządzaj projektami inwestycyjnymi.') }}</p>
                    </a>
                    
                    <!-- Zarządzanie inwestycjami -->
                    <a href="{{ route('investments.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300">
                        <h4 class="font-semibold mb-2">{{ __('Inwestycje') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Monitoruj inwestycje na platformie.') }}</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
