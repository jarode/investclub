<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inwestycje użytkownika') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6">
                    <a href="{{ route('users.show', $user) }}" class="text-blue-500 hover:text-blue-700">
                        &larr; Powrót do profilu użytkownika
                    </a>
                </div>
                
                <h1 class="text-2xl font-bold mb-4">Inwestycje użytkownika: {{ $user->name }}</h1>
                
                <div class="mt-4">
                    <p class="text-gray-600">Ta strona jest testowym przykładem użycia Gate::allows() w metodzie kontrolera.</p>
                    <p class="mt-2">Dostęp do tej strony jest kontrolowany przez metodę 'manageInvestments' w UserPolicy.</p>
                    <p class="mt-2">Tylko użytkownicy z rolami: administrator, manager lub inwestor mogą uzyskać dostęp.</p>
                </div>
                
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Symulowane inwestycje użytkownika</h2>
                    
                    <div class="bg-gray-100 p-4 rounded-lg mb-4">
                        <h3 class="font-semibold">Inwestycja #1</h3>
                        <p class="text-gray-600">Kwota: 1000 PLN</p>
                        <p class="text-gray-600">Status: Aktywna</p>
                    </div>
                    
                    <div class="bg-gray-100 p-4 rounded-lg mb-4">
                        <h3 class="font-semibold">Inwestycja #2</h3>
                        <p class="text-gray-600">Kwota: 2500 PLN</p>
                        <p class="text-gray-600">Status: Oczekująca</p>
                    </div>
                    
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h3 class="font-semibold">Inwestycja #3</h3>
                        <p class="text-gray-600">Kwota: 5000 PLN</p>
                        <p class="text-gray-600">Status: Zakończona</p>
                    </div>
                </div>
                
                @can('viewFinancialData', $user)
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Dane finansowe (widoczne tylko dla uprawnionych ról)</h2>
                    
                    <div class="bg-yellow-100 p-4 rounded-lg">
                        <p class="font-semibold">Wartość portfela: {{ number_format($user->wallet_balance, 2) }} PLN</p>
                        <p>Zysk całkowity: +15.5%</p>
                        <p>Średni roczny zysk: +8.2%</p>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
