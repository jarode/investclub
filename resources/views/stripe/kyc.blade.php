<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Weryfikacja KYC') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">{{ __('Weryfikacja Know Your Customer (KYC)') }}</h3>

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-6 text-gray-600">
                    <p class="mb-4">
                        Aby korzystać z platformy InvestClub, musimy zweryfikować Twoją tożsamość zgodnie z wymogami regulacyjnymi.
                        Proces weryfikacji KYC (Know Your Customer) jest prosty i zabezpieczony.
                    </p>
                
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Twoje dane są bezpieczne i będą używane wyłącznie do celów weryfikacji.
                                </p>
                            </div>
                        </div>
                    </div>
                
                    <h4 class="text-lg font-medium mb-3">Czego będziesz potrzebować:</h4>
                    <ul class="list-disc pl-5 mb-6 space-y-2">
                        <li>Ważnego dokumentu tożsamości (dowód osobisty, paszport)</li>
                        <li>Urządzenia z kamerą (do zrobienia zdjęć dokumentów)</li>
                        <li>Kilku minut Twojego czasu</li>
                    </ul>
                
                    <h4 class="text-lg font-medium mb-3">Przebieg procesu:</h4>
                    <ol class="list-decimal pl-5 mb-6 space-y-2">
                        <li>Po kliknięciu przycisku "Rozpocznij weryfikację" zostaniesz przekierowany na bezpieczną stronę Stripe</li>
                        <li>Zrobisz zdjęcie swojego dokumentu tożsamości</li>
                        <li>Zrobisz selfie w celu porównania z dokumentem</li>
                        <li>Po zakończeniu procesu zostaniesz przekierowany z powrotem do aplikacji</li>
                        <li>Twoja tożsamość zostanie zweryfikowana w ciągu 24 godzin</li>
                    </ol>
                </div>

                <div class="mt-6">
                    @if(auth()->user()->kyc_status === 'verified')
                        <div class="bg-green-50 border-l-4 border-green-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        Twoja tożsamość została już pomyślnie zweryfikowana.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->kyc_status === 'pending')
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-8.414l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L9 9.586V5a1 1 0 012 0v4.586z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Twoja weryfikacja jest w trakcie przetwarzania. Prosimy o cierpliwość, ten proces może potrwać do 24 godzin.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('kyc.verify') }}" 
                           class="inline-block bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600">
                            Przeprowadź weryfikację ponownie
                        </a>
                    @else
                        <a href="{{ route('kyc.verify') }}" 
                           class="inline-block bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
                            Rozpocznij weryfikację
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 