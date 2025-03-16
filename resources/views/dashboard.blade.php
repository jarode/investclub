<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-4">{{ __('Witaj w InvestClub') }}</h3>
                <p class="mb-4">
                    Witaj, {{ auth()->user()->name }}! Platforma InvestClub umożliwia inwestorom wyrażanie zainteresowania projektami i dokonywanie inwestycji.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Status weryfikacji KYC -->
                    <div class="border rounded-lg p-4 h-full">
                        <h4 class="font-semibold mb-2">Status weryfikacji KYC</h4>
                        
                        @if ($kycStatus === 'verified')
                            <div class="flex items-center text-green-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Zweryfikowany</span>
                            </div>
                            <p class="text-sm text-gray-600">Twoja tożsamość została pomyślnie zweryfikowana.</p>
                        @elseif ($kycStatus === 'pending')
                            <div class="flex items-center text-yellow-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>W trakcie weryfikacji</span>
                            </div>
                            <p class="text-sm text-gray-600">Twoja weryfikacja jest w trakcie przetwarzania.</p>
                        @else
                            <div class="flex items-center text-red-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>Niezweryfikowany</span>
                            </div>
                            <p class="text-sm text-gray-600">Musisz przeprowadzić weryfikację KYC, aby uzyskać pełny dostęp.</p>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('kyc.verify') }}" class="text-indigo-600 hover:text-indigo-800">
                                @if ($kycStatus !== 'verified')
                                    Przeprowadź weryfikację KYC
                                @else
                                    Podgląd statusu KYC
                                @endif
                            </a>
                        </div>
                    </div>
                    
                    <!-- Status subskrypcji -->
                    <div class="border rounded-lg p-4 h-full">
                        <h4 class="font-semibold mb-2">Status subskrypcji</h4>
                        
                        @if ($subscriptionStatus === 'active')
                            <div class="flex items-center text-green-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Aktywna</span>
                            </div>
                            <p class="text-sm text-gray-600">Twoja subskrypcja jest aktywna. Masz pełny dostęp do platformy.</p>
                        @elseif ($subscriptionStatus === 'cancelled')
                            <div class="flex items-center text-yellow-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Anulowana</span>
                            </div>
                            <p class="text-sm text-gray-600">Twoja subskrypcja została anulowana, ale pozostaje aktywna do końca okresu rozliczeniowego.</p>
                        @else
                            <div class="flex items-center text-red-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>Nieaktywna</span>
                            </div>
                            <p class="text-sm text-gray-600">Musisz aktywować subskrypcję, aby uzyskać pełny dostęp do platformy.</p>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('subscription') }}" class="text-indigo-600 hover:text-indigo-800">
                                @if ($subscriptionStatus !== 'active')
                                    Aktywuj subskrypcję
                                @else
                                    Zarządzaj subskrypcją
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">{{ __('Dostępne funkcje') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('projects.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full {{ $kycStatus !== 'verified' || $subscriptionStatus !== 'active' ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <h4 class="font-semibold mb-2">Projekty inwestycyjne</h4>
                        <p class="text-sm text-gray-600">Przeglądaj projekty inwestycyjne dostępne na platformie.</p>
                        @if ($kycStatus !== 'verified' || $subscriptionStatus !== 'active')
                            <div class="mt-2 text-xs text-red-600">
                                Wymaga weryfikacji KYC i aktywnej subskrypcji
                            </div>
                        @endif
                    </a>
                    
                    <a href="{{ route('investments.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full {{ $kycStatus !== 'verified' || $subscriptionStatus !== 'active' ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <h4 class="font-semibold mb-2">Moje inwestycje</h4>
                        <p class="text-sm text-gray-600">Zarządzaj swoimi inwestycjami i śledź ich status.</p>
                        @if ($kycStatus !== 'verified' || $subscriptionStatus !== 'active')
                            <div class="mt-2 text-xs text-red-600">
                                Wymaga weryfikacji KYC i aktywnej subskrypcji
                            </div>
                        @endif
                    </a>
                    
                    <a href="{{ route('billing.portal') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full {{ $subscriptionStatus !== 'active' ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <h4 class="font-semibold mb-2">Portal płatności</h4>
                        <p class="text-sm text-gray-600">Zarządzaj swoimi metodami płatności i fakturami.</p>
                        @if ($subscriptionStatus !== 'active')
                            <div class="mt-2 text-xs text-red-600">
                                Wymaga aktywnej subskrypcji
                            </div>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
