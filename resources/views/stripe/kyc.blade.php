<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Weryfikacja KYC') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session('warning'))
                    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        {{ session('warning') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-6">
                    <h3 class="text-lg font-medium mb-2">Status weryfikacji KYC</h3>
                    
                    @if ($kycStatus === 'verified')
                        <div class="flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Weryfikacja KYC zakończona pomyślnie</span>
                        </div>
                        <p class="mt-2 text-gray-600">Masz pełny dostęp do platformy InvestClub.</p>
                    @elseif ($kycStatus === 'pending')
                        <div class="flex items-center text-yellow-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Weryfikacja KYC w trakcie przetwarzania</span>
                        </div>
                        <p class="mt-2 text-gray-600">Twoja weryfikacja jest w trakcie przetwarzania. Proces może potrwać do 24 godzin.</p>
                    @else
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Weryfikacja KYC nie została przeprowadzona</span>
                        </div>
                        <p class="mt-2 text-gray-600">Aby uzyskać pełny dostęp do platformy InvestClub, musisz przejść weryfikację KYC.</p>
                    @endif
                </div>

                @if ($kycStatus !== 'verified')
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Przeprowadź weryfikację KYC</h3>
                        <p class="mb-4 text-gray-600">
                            Weryfikacja KYC (Know Your Customer) jest wymagana przez przepisy prawne i pomaga nam zapewnić bezpieczeństwo wszystkim uczestnikom. 
                            Zostaniesz przekierowany do bezpiecznego procesu weryfikacji obsługiwanego przez Stripe.
                        </p>
                        
                        <form action="{{ route('kyc.start') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
                                Rozpocznij weryfikację KYC
                            </button>
                        </form>
                    </div>
                @endif

                <div>
                    <h3 class="text-lg font-medium mb-2">Dlaczego weryfikacja KYC jest ważna?</h3>
                    <p class="text-gray-600">
                        Weryfikacja KYC jest istotnym elementem bezpieczeństwa w branży inwestycyjnej. Pomaga nam:
                    </p>
                    <ul class="mt-2 space-y-1 list-disc list-inside text-gray-600">
                        <li>Zapobiegać oszustwom i praniu pieniędzy</li>
                        <li>Spełniać wymogi prawne i regulacyjne</li>
                        <li>Zwiększać bezpieczeństwo i zaufanie wszystkich uczestników platformy</li>
                        <li>Zapewniać najwyższe standardy bezpieczeństwa transakcji</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 