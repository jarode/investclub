@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 lg:p-8">
                <h1 class="text-2xl font-medium text-gray-900">
                    Wybierz plan subskrypcji
                </h1>

                @if(session('error'))
                    <div class="mt-4 bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    {{ session('error') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->stripe_subscription_status === 'active')
                    <div class="mt-4 bg-green-50 border-l-4 border-green-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    Masz aktywną subskrypcję: <strong>{{ auth()->user()->plan_type === 'free-investor' ? 'Plan darmowy' : (auth()->user()->plan_type === 'premium-investor' ? 'Plan premium dla inwestorów' : 'Plan premium dla właścicieli') }}</strong>. 
                                    Możesz zmienić plan lub zarządzać swoją subskrypcją.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center space-y-4">
                        <div>
                            <x-button onclick="openUpdateSubscriptionPortal()" class="bg-indigo-600 hover:bg-indigo-700">
                                Zmień plan subskrypcji
                            </x-button>
                        </div>
                        <div>
                            <x-button onclick="openCustomerPortal()">
                                Zarządzaj subskrypcją
                            </x-button>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold text-center mb-6">Dostępne plany:</h2>
                        <div class="grid md:grid-cols-3 gap-6">
                            <!-- Plan Darmowy -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ auth()->user()->plan_type === 'free-investor' ? 'border-2 border-green-500' : '' }}">
                                <div class="p-6">
                                    <h2 class="text-xl font-semibold text-gray-900">I-Free</h2>
                                    <p class="mt-2 text-gray-600">Darmowy plan dla inwestorów</p>
                                    <p class="mt-4 text-3xl font-bold text-gray-900">0 zł</p>
                                    <ul class="mt-6 space-y-4">
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Przeglądanie projektów</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Wyrażanie zainteresowania</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Ograniczony dostęp do szczegółów</p>
                                        </li>
                                    </ul>
                                    <div class="mt-8">
                                        @if(auth()->user()->plan_type === 'free-investor')
                                            <span class="w-full block py-2 px-4 bg-gray-100 text-gray-700 text-center rounded-md font-medium">
                                                Aktualny plan
                                            </span>
                                        @else
                                            <x-button onclick="changeToFreePlan()" class="w-full justify-center">
                                                Wybierz plan darmowy
                                            </x-button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Plan Premium dla Inwestorów -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ auth()->user()->plan_type === 'premium-investor' ? 'border-2 border-green-500' : 'border-2 border-indigo-500' }}">
                                <div class="p-6">
                                    <div class="absolute top-0 right-0 bg-indigo-500 text-white px-2 py-1 text-sm rounded-bl">Popularny</div>
                                    <h2 class="text-xl font-semibold text-gray-900">I-Premium</h2>
                                    <p class="mt-2 text-gray-600">Plan premium dla inwestorów</p>
                                    <p class="mt-4 text-3xl font-bold text-gray-900">500 zł<span class="text-sm text-gray-500">/miesiąc</span></p>
                                    <ul class="mt-6 space-y-4">
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Wszystko z planu I-Free</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Priorytetowy dostęp do projektów</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Zaawansowane analizy</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Dostęp do ekskluzywnych projektów</p>
                                        </li>
                                    </ul>
                                    <div class="mt-8">
                                        @if(auth()->user()->plan_type === 'premium-investor')
                                            <span class="w-full block py-2 px-4 bg-gray-100 text-gray-700 text-center rounded-md font-medium">
                                                Aktualny plan
                                            </span>
                                        @else
                                            <x-button onclick="changeToPremiumInvestorPlan()" class="w-full justify-center bg-indigo-600 hover:bg-indigo-700">
                                                Wybierz plan
                                            </x-button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Plan Premium dla Właścicieli -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ auth()->user()->plan_type === 'premium-owner' ? 'border-2 border-green-500' : '' }}">
                                <div class="p-6">
                                    <h2 class="text-xl font-semibold text-gray-900">O-Premium</h2>
                                    <p class="mt-2 text-gray-600">Plan premium dla właścicieli projektów</p>
                                    <p class="mt-4 text-3xl font-bold text-gray-900">1000 zł<span class="text-sm text-gray-500">/miesiąc</span></p>
                                    <ul class="mt-6 space-y-4">
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Możliwość dodawania projektów</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Dostęp do bazy inwestorów premium</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Narzędzia do analizy zainteresowania</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">Wsparcie w procesie finansowania</p>
                                        </li>
                                    </ul>
                                    <div class="mt-8">
                                        @if(auth()->user()->plan_type === 'premium-owner')
                                            <span class="w-full block py-2 px-4 bg-gray-100 text-gray-700 text-center rounded-md font-medium">
                                                Aktualny plan
                                            </span>
                                        @else
                                            <x-button onclick="changeToPremiumOwnerPlan()" class="w-full justify-center">
                                                Wybierz plan
                                            </x-button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 grid md:grid-cols-3 gap-6">
                        <!-- Plan Darmowy -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-xl font-semibold text-gray-900">I-Free</h2>
                                <p class="mt-2 text-gray-600">Darmowy plan dla inwestorów</p>
                                <p class="mt-4 text-3xl font-bold text-gray-900">0 zł</p>
                                <ul class="mt-6 space-y-4">
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Przeglądanie projektów</p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Wyrażanie zainteresowania</p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Ograniczony dostęp do szczegółów</p>
                                    </li>
                                </ul>
                                <div class="mt-8">
                                    <x-button onclick="activateFreePlan()" class="w-full justify-center">
                                        Aktywuj plan darmowy
                                    </x-button>
                                </div>
                            </div>
                        </div>

                        <!-- Plan Premium dla Inwestorów -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-indigo-500">
                            <div class="p-6">
                                <div class="absolute top-0 right-0 bg-indigo-500 text-white px-2 py-1 text-sm rounded-bl">Popularny</div>
                                <h2 class="text-xl font-semibold text-gray-900">I-Premium</h2>
                                <p class="mt-2 text-gray-600">Plan premium dla inwestorów</p>
                                <p class="mt-4 text-3xl font-bold text-gray-900">500 zł<span class="text-sm text-gray-500">/miesiąc</span></p>
                                <ul class="mt-6 space-y-4">
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Wszystko z planu I-Free</p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Priorytetowy dostęp do projektów</p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Zaawansowane analizy</p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Dostęp do ekskluzywnych projektów</p>
                                    </li>
                                </ul>
                                <div class="mt-8">
                                    <x-button onclick="activatePremiumInvestorPlan()" class="w-full justify-center bg-indigo-600 hover:bg-indigo-700">
                                        Wybierz plan
                                    </x-button>
                                </div>
                            </div>
                        </div>

                        <!-- Plan Premium dla Właścicieli -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-xl font-semibold text-gray-900">O-Premium</h2>
                                <p class="mt-2 text-gray-600">Plan premium dla właścicieli projektów</p>
                                <p class="mt-4 text-3xl font-bold text-gray-900">1000 zł<span class="text-sm text-gray-500">/miesiąc</span></p>
                                <ul class="mt-6 space-y-4">
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Możliwość dodawania projektów</p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Dostęp do bazy inwestorów premium</p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Narzędzia do analizy zainteresowania</p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">Wsparcie w procesie finansowania</p>
                                    </li>
                                </ul>
                                <div class="mt-8">
                                    <x-button onclick="activatePremiumOwnerPlan()" class="w-full justify-center">
                                        Wybierz plan
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function activateFreePlan() {
    fetch('/stripe/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            price_id: '{{ config('stripe.products.free_investor.price_id') }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.url) {
            window.location.href = data.url;
        }
    })
    .catch(error => {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas aktywacji planu.');
    });
}

function activatePremiumInvestorPlan() {
    fetch('/stripe/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            price_id: '{{ config('stripe.products.premium_investor.price_id') }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.url) {
            window.location.href = data.url;
        }
    })
    .catch(error => {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas aktywacji planu premium dla inwestorów.');
    });
}

function activatePremiumOwnerPlan() {
    fetch('/stripe/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            price_id: '{{ config('stripe.products.premium_owner.price_id') }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.url) {
            window.location.href = data.url;
        }
    })
    .catch(error => {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas aktywacji planu premium dla właścicieli.');
    });
}

function openCustomerPortal() {
    fetch('/stripe/portal', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.url) {
            window.location.href = data.url;
        }
    })
    .catch(error => {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas otwierania portalu klienta.');
    });
}

function openUpdateSubscriptionPortal() {
    fetch('/stripe/update-portal', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.url) {
            window.location.href = data.url;
        }
    })
    .catch(error => {
        console.error('Błąd:', error);
        alert('Wystąpił błąd podczas otwierania portalu aktualizacji subskrypcji.');
    });
}

function changeToFreePlan() {
    if (confirm('Czy na pewno chcesz zmienić plan na darmowy?')) {
        openUpdateSubscriptionPortal();
    }
}

function changeToPremiumInvestorPlan() {
    if (confirm('Czy na pewno chcesz zmienić plan na I-Premium?')) {
        openUpdateSubscriptionPortal();
    }
}

function changeToPremiumOwnerPlan() {
    if (confirm('Czy na pewno chcesz zmienić plan na O-Premium?')) {
        openUpdateSubscriptionPortal();
    }
}
</script>
@endpush 