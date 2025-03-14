<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dodaj użytkownika') }}
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
                
                <h1 class="text-2xl font-bold mb-6">Dodaj nowego użytkownika</h1>
                
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Imię -->
                        <div>
                            <x-label for="name" value="{{ __('Imię') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error for="name" class="mt-2" />
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <x-label for="email" value="{{ __('Email') }}" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error for="email" class="mt-2" />
                        </div>
                        
                        <!-- Hasło -->
                        <div>
                            <x-label for="password" value="{{ __('Hasło') }}" />
                            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error for="password" class="mt-2" />
                        </div>
                        
                        <!-- Potwierdzenie hasła -->
                        <div>
                            <x-label for="password_confirmation" value="{{ __('Potwierdź hasło') }}" />
                            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error for="password_confirmation" class="mt-2" />
                        </div>
                        
                        <!-- Rola -->
                        <div>
                            <x-label for="role" value="{{ __('Rola') }}" />
                            <select id="role" name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="user">Użytkownik</option>
                                <option value="investor">Inwestor</option>
                                <option value="accountant">Księgowy</option>
                                <option value="manager">Manager</option>
                                @if(Auth::user()->isAdmin())
                                    <option value="admin">Administrator</option>
                                @endif
                            </select>
                            <x-input-error for="role" class="mt-2" />
                        </div>
                        
                        <!-- Status weryfikacji -->
                        <div>
                            <x-label for="verification_status" value="{{ __('Status weryfikacji') }}" />
                            <select id="verification_status" name="verification_status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="unverified">Niezweryfikowany</option>
                                <option value="pending">Oczekujący</option>
                                <option value="verified">Zweryfikowany</option>
                            </select>
                            <x-input-error for="verification_status" class="mt-2" />
                        </div>
                        
                        <!-- Stan portfela -->
                        <div>
                            <x-label for="wallet_balance" value="{{ __('Stan portfela (PLN)') }}" />
                            <x-input id="wallet_balance" class="block mt-1 w-full" type="number" step="0.01" name="wallet_balance" :value="old('wallet_balance', 0)" required />
                            <x-input-error for="wallet_balance" class="mt-2" />
                        </div>
                        
                        <!-- Status KYC -->
                        <div>
                            <x-label for="kyc_status" value="{{ __('Status KYC') }}" />
                            <select id="kyc_status" name="kyc_status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="unverified">Niezweryfikowany</option>
                                <option value="pending">Oczekujący</option>
                                <option value="verified">Zweryfikowany</option>
                            </select>
                            <x-input-error for="kyc_status" class="mt-2" />
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <x-button>
                            {{ __('Utwórz użytkownika') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
