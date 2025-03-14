<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Szczegóły użytkownika') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6">
                    <a href="{{ route('users.index') }}" class="text-blue-500 hover:text-blue-700">
                        &larr; Powrót do listy użytkowników
                    </a>
                </div>
                
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3 pr-4">
                        <div class="flex justify-center">
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-full h-48 w-48 object-cover">
                        </div>
                    </div>
                    
                    <div class="md:w-2/3">
                        <h1 class="text-2xl font-bold mb-4">{{ $user->name }}</h1>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600 text-sm">Email:</p>
                                <p class="font-semibold">{{ $user->email }}</p>
                            </div>
                            
                            <div>
                                <p class="text-gray-600 text-sm">Rola:</p>
                                <p class="font-semibold">{{ $user->role }}</p>
                            </div>
                            
                            <div>
                                <p class="text-gray-600 text-sm">Status weryfikacji:</p>
                                <p class="font-semibold">{{ $user->verification_status }}</p>
                            </div>
                            
                            <div>
                                <p class="text-gray-600 text-sm">Status KYC:</p>
                                <p class="font-semibold">{{ $user->kyc_status }}</p>
                            </div>
                            
                            <div>
                                <p class="text-gray-600 text-sm">Data utworzenia:</p>
                                <p class="font-semibold">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            
                            @can('viewFinancialData', $user)
                            <div>
                                <p class="text-gray-600 text-sm">Stan portfela:</p>
                                <p class="font-semibold">{{ number_format($user->wallet_balance, 2) }} PLN</p>
                            </div>
                            @endcan
                        </div>
                        
                        <div class="mt-8 flex space-x-4">
                            @can('update', $user)
                                <a href="{{ route('users.edit', $user) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Edytuj
                                </a>
                            @endcan
                            
                            @can('delete', $user)
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Usuń
                                    </button>
                                </form>
                            @endcan
                            
                            @can('manageInvestments', $user)
                                <a href="{{ route('users.investments', $user) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    Zarządzaj inwestycjami
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
