<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subskrypcja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">{{ __('Subskrypcja') }}</h3>

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

                @if (Auth::user()->stripe_subscription_status === 'active')
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        <h4 class="font-bold text-lg mb-2">Twoja subskrypcja jest aktywna</h4>
                        
                        @if (Auth::user()->plan_type === 'premium-investor' || Auth::user()->plan_type === 'premium-owner')
                            <!-- Płatna subskrypcja -->
                            <p class="mb-3">Obecnie korzystasz z planu 
                                <span class="font-semibold">
                                    @if (Auth::user()->plan_type === 'premium-investor')
                                        I-Premium
                                    @elseif (Auth::user()->plan_type === 'premium-owner')
                                        O-Premium
                                    @else
                                        Premium
                                    @endif
                                </span>
                                
                                @if (Auth::user()->cancellation_requested)
                                    <span class="ml-2 px-2 py-1 text-xs bg-yellow-200 text-yellow-800 rounded">Anulowanie w toku</span>
                                @endif
                            </p>
                            
                            @if (Auth::user()->cancellation_requested)
                                <p class="mb-4 text-sm italic">Twoja subskrypcja została anulowana, ale będzie aktywna do końca okresu rozliczeniowego.</p>
                            @endif
                            
                            <div class="flex flex-col space-y-2 md:flex-row md:space-y-0 md:space-x-4">
                                <a href="{{ route('billing.portal') }}" class="inline-block bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 text-center">
                                    Zarządzaj płatnościami
                                </a>
                                
                                @if (!Auth::user()->cancellation_requested)
                                <form action="{{ route('subscription.cancel') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full md:w-auto bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
                                        Anuluj subskrypcję
                                    </button>
                                </form>
                                @endif
                            </div>
                            
                            <!-- Opcja zmiany planu na wyższy -->
                            @if (Auth::user()->plan_type === 'premium-investor' && !Auth::user()->cancellation_requested)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h4 class="text-lg font-medium mb-3">Zmień na wyższy plan</h4>
                                    <div class="border rounded-lg p-6 bg-gray-50 hover:shadow-lg transition duration-300">
                                        <h4 class="text-xl font-semibold mb-2">O-Premium</h4>
                                        <p class="text-gray-600 mb-4">Pełny dostęp do platformy dla właścicieli projektów.</p>
                                        <p class="text-2xl font-bold mb-4">1000 zł / miesiąc</p>
                                        <ul class="mb-6 space-y-2">
                                            <li class="flex items-center">
                                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Możliwość dodawania projektów
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Dostęp do bazy inwestorów premium
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Narzędzia do analizy zainteresowania
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Wsparcie w procesie pozyskiwania finansowania
                                            </li>
                                        </ul>
                                        <div class="flex justify-center mt-8">
                                            <button onclick="createCheckoutSession('{{ config('stripe.products.premium_owner.price_id') }}')" 
                                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                                                Wybierz plan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Darmowa subskrypcja -->
                            <p class="mb-3">Obecnie korzystasz z <span class="font-semibold">darmowego planu I-Free</span>.</p>
                            <p class="mb-4">Chcesz uzyskać więcej możliwości? Rozważ przejście na plan premium:</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Plan Premium dla Inwestora -->
                                <div class="border rounded-lg p-6 bg-gray-50 hover:shadow-lg transition duration-300">
                                    <h4 class="text-xl font-semibold mb-2">I-Premium</h4>
                                    <p class="text-gray-600 mb-4">Pełny dostęp do platformy dla inwestorów z dodatkowymi korzyściami.</p>
                                    <p class="text-2xl font-bold mb-4">500 zł / miesiąc</p>
                                    <ul class="mb-6 space-y-2">
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Priorytetowy dostęp do nowych projektów
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Zaawansowane analizy i raporty
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Dostęp do ekskluzywnych projektów
                                        </li>
                                    </ul>
                                    <div class="flex justify-center mt-8">
                                        <button onclick="createCheckoutSession('{{ config('stripe.products.premium_investor.price_id') }}')" 
                                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                                            Wybierz plan
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Plan Premium dla Właściciela Projektu -->
                                <div class="border rounded-lg p-6 bg-gray-50 hover:shadow-lg transition duration-300">
                                    <h4 class="text-xl font-semibold mb-2">O-Premium</h4>
                                    <p class="text-gray-600 mb-4">Pełny dostęp do platformy dla właścicieli projektów.</p>
                                    <p class="text-2xl font-bold mb-4">1000 zł / miesiąc</p>
                                    <ul class="mb-6 space-y-2">
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Możliwość dodawania projektów
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Dostęp do bazy inwestorów premium
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Narzędzia do analizy zainteresowania
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Wsparcie w procesie pozyskiwania finansowania
                                        </li>
                                    </ul>
                                    <div class="flex justify-center mt-8">
                                        <button onclick="createCheckoutSession('{{ config('stripe.products.premium_owner.price_id') }}')" 
                                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                                            Wybierz plan
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Przycisk anulowania darmowej subskrypcji -->
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <form action="{{ route('subscription.cancel') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
                                        Anuluj darmową subskrypcję
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                        <p><strong>Informacja:</strong> Wybór darmowego planu aktywuje go natychmiast bez dodatkowych kroków. 
                        Plany płatne wymagają podania danych karty na stronie Stripe.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Plan Darmowy dla Inwestora -->
                        <div class="border rounded-lg p-6 hover:shadow-lg transition duration-300">
                            <h4 class="text-xl font-semibold mb-2">I-Free</h4>
                            <p class="text-gray-600 mb-4">Podstawowy dostęp do platformy dla inwestorów.</p>
                            <p class="text-2xl font-bold mb-4">0 zł / miesiąc</p>
                            <ul class="mb-6 space-y-2">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Przeglądanie projektów
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Wyrażanie zainteresowania
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Ograniczony dostęp do szczegółów
                                </li>
                            </ul>
                            <div class="flex justify-center mt-8">
                                <button onclick="createCheckoutSession('{{ config('stripe.products.premium_investor.price_id') }}')" 
                                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                                    Wybierz plan
                                </button>
                            </div>
                        </div>

                        <!-- Plan Premium dla Inwestora -->
                        <div class="border rounded-lg p-6 bg-gray-50 hover:shadow-lg transition duration-300">
                            <h4 class="text-xl font-semibold mb-2">I-Premium</h4>
                            <p class="text-gray-600 mb-4">Pełny dostęp do platformy dla inwestorów z dodatkowymi korzyściami.</p>
                            <p class="text-2xl font-bold mb-4">500 zł / miesiąc</p>
                            <ul class="mb-6 space-y-2">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Wszystko z planu I-Free
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Priorytetowy dostęp do nowych projektów
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Zaawansowane analizy i raporty
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Dostęp do ekskluzywnych projektów
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Indywidualne doradztwo
                                </li>
                            </ul>
                            <div class="flex justify-center mt-8">
                                <button onclick="createCheckoutSession('{{ config('stripe.products.premium_investor.price_id') }}')" 
                                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                                    Wybierz plan
                                </button>
                            </div>
                        </div>
                        
                        <!-- Plan Premium dla Właściciela Projektu -->
                        <div class="border rounded-lg p-6 bg-gray-50 hover:shadow-lg transition duration-300">
                            <h4 class="text-xl font-semibold mb-2">O-Premium</h4>
                            <p class="text-gray-600 mb-4">Pełny dostęp do platformy dla właścicieli projektów.</p>
                            <p class="text-2xl font-bold mb-4">1000 zł / miesiąc</p>
                            <ul class="mb-6 space-y-2">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Możliwość dodawania projektów
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Dostęp do bazy inwestorów premium
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Narzędzia do analizy zainteresowania
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Wsparcie w procesie pozyskiwania finansowania
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Promocja projektu na platformie
                                </li>
                            </ul>
                            <div class="flex justify-center mt-8">
                                <button onclick="createCheckoutSession('{{ config('stripe.products.premium_owner.price_id') }}')" 
                                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                                    Wybierz plan
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicjalizacja Stripe
            const stripe = Stripe('{{ config('stripe.key') }}');
            
            // Reszta kodu JavaScript...
        });
    </script>
    @endpush
</x-app-layout> 