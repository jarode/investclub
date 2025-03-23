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
                                <span>Nieukończona weryfikacja</span>
                            </div>
                            <p class="text-sm text-gray-600">Rozpocznij proces weryfikacji KYC, aby uzyskać pełny dostęp do platformy.</p>
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
                        
                        @if (auth()->user()->hasActiveSubscription())
                            <div class="flex items-center text-green-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Aktywna</span>
                                
                                @if (auth()->user()->hasSubscriptionPendingCancellation())
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Zakończy się w przyszłym okresie rozliczeniowym
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-1">
                                <strong>Plan:</strong> 
                                @if (auth()->user()->hasPlanType('free-investor'))
                                    I-Free (Darmowy plan dla inwestorów)
                                @elseif (auth()->user()->hasPlanType('premium-investor'))
                                    I-Premium (Premium dla inwestorów)
                                @elseif (auth()->user()->hasPlanType('premium-owner'))
                                    O-Premium (Premium dla właścicieli projektów)
                                @else
                                    {{ auth()->user()->plan_type }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-600">Twoja subskrypcja jest aktywna. Masz dostęp do funkcji zgodnych z Twoim planem.</p>
                            
                            @if($nextPaymentDate && !auth()->user()->hasFreePlan())
                                <p class="text-sm text-gray-600 mt-2">
                                    <strong>Następna płatność:</strong> {{ $nextPaymentDate }}
                                </p>
                            @endif
                        @elseif (auth()->user()->stripe_subscription_status === 'past_due')
                            <div class="flex items-center text-red-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Problem z płatnością</span>
                            </div>
                            <p class="text-sm text-gray-600">Wystąpił problem z płatnością Twojej subskrypcji. Zaktualizuj metodę płatności, aby zachować dostęp do platformy.</p>
                        @elseif (auth()->user()->stripe_subscription_status === 'cancelled')
                            <div class="flex items-center text-yellow-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Anulowana</span>
                            </div>
                            <p class="text-sm text-gray-600">Twoja subskrypcja została anulowana. Możesz aktywować nową subskrypcję, aby odzyskać dostęp do platformy.</p>
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
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('subscription') }}" class="text-indigo-600 hover:text-indigo-800 px-3 py-1 border border-indigo-600 rounded-md text-sm">
                                    @if (!auth()->user()->hasActiveSubscription())
                                        Wybierz plan
                                    @else
                                        Zmień plan
                                    @endif
                                </a>
                                
                                @if (auth()->user()->stripe_customer_id)
                                    <form action="{{ route('stripe.portal') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 px-3 py-1 border border-blue-600 rounded-md text-sm">
                                            Portal płatności Stripe
                                        </button>
                                    </form>
                                @endif
                            </div>
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
                    
                    <a href="{{ route('stripe.portal') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full {{ $subscriptionStatus !== 'active' ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <h4 class="font-semibold mb-2">Portal płatności</h4>
                        <p class="text-sm text-gray-600">Zarządzaj swoimi metodami płatności i fakturami.</p>
                        @if ($subscriptionStatus !== 'active')
                            <div class="mt-2 text-xs text-red-600">
                                Wymaga aktywnej subskrypcji
                            </div>
                        @endif
                    </a>
                    
                    @if (auth()->user()->isPremiumInvestor() || auth()->user()->isProjectOwner())
                    <a href="{{ route('projects.exclusive') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full bg-purple-50 border-purple-200">
                        <div class="flex items-center mb-2">
                            <h4 class="font-semibold">Projekty ekskluzywne</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Premium</span>
                        </div>
                        <p class="text-sm text-gray-600">Uzyskaj dostęp do ekskluzywnych projektów dostępnych tylko dla użytkowników premium.</p>
                    </a>
                    @endif
                    
                    @if (auth()->user()->isProjectOwner() || auth()->user()->isAdmin())
                    <a href="{{ route('project.dashboard') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full bg-blue-50 border-blue-200">
                        <div class="flex items-center mb-2">
                            <h4 class="font-semibold">Panel właściciela projektów</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">O-Premium</span>
                        </div>
                        <p class="text-sm text-gray-600">Zarządzaj swoimi projektami, śledź inwestycje i analizuj wyniki.</p>
                    </a>
                    
                    <a href="{{ route('projects.create') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full bg-blue-50 border-blue-200">
                        <div class="flex items-center mb-2">
                            <h4 class="font-semibold">Utwórz nowy projekt</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">O-Premium</span>
                        </div>
                        <p class="text-sm text-gray-600">Dodaj nowy projekt inwestycyjny do platformy.</p>
                    </a>
                    @endif
                </div>
            </div>
            
            @if (!auth()->user()->canManageProjects() && !auth()->user()->isProjectOwner())
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mt-6 border-t-4 border-blue-400">
                <div class="flex items-start">
                    <div class="flex-shrink-0 pt-1">
                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium mb-2">{{ __('Chcesz dodawać własne projekty?') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Przejdź na plan O-Premium, aby uzyskać możliwość dodawania i zarządzania własnymi projektami inwestycyjnymi.
                            Otrzymasz dostęp do zaawansowanych narzędzi analitycznych i będziesz mógł dotrzeć do inwestorów na naszej platformie.
                        </p>
                        <a href="{{ route('subscription') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 active:bg-blue-600 transition">
                            {{ __('Przejdź na O-Premium') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
        </div>
    </div>
</x-app-layout>
