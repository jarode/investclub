<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <h1 class="text-2xl font-medium text-gray-900">
                        {{ __('Choose subscription plan') }}
                    </h1>

                    @if(session('error'))
                        <div class="mt-4 bg-red-50 border-l-4 border-red-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.873-1.756 3.157-1.756 4.03 0l3.476 7.01c.269.541.12 1.187-.29 1.54a.968.968 0 01-1.39-.012L12 8.517l-2.312 2.516a.968.968 0 01-1.39.013c-.41-.354-.558-1-.29-1.54l3.477-7.01z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mt-4 bg-green-50 border-l-4 border-green-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.485 3.495c.873-1.756 3.157-1.756 4.03 0l3.476 7.01c.269.541.12 1.187-.29 1.54a.968.968 0 01-1.39-.012L12 9.517l-2.312 2.516a.968.968 0 01-1.39.013c-.41-.354-.558-1-.29-1.54l3.477-7.01z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
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
                                        {{ __('You have an active subscription') }}: <strong>{{ auth()->user()->plan_type === 'free-investor' ? __('Free plan') : (auth()->user()->plan_type === 'premium-investor' ? __('Premium plan for investors') : __('Premium plan for owners')) }}</strong>. 
                                        {{ __('You can change your plan or manage your subscription.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 text-center space-y-4">
                            <div>
                                <x-button onclick="openUpdateSubscriptionPortal()" class="bg-indigo-600 hover:bg-indigo-700">
                                    {{ __('Change subscription plan') }}
                                </x-button>
                            </div>
                            <div>
                                <x-button onclick="openCustomerPortal()">
                                    {{ __('Manage subscription') }}
                                </x-button>
                            </div>
                        </div>
                        
                        <div class="mt-8">
                            <h2 class="text-xl font-semibold text-center mb-6">{{ __('Available plans') }}:</h2>
                            <div class="grid md:grid-cols-3 gap-6">
                                <!-- Plan Darmowy -->
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ auth()->user()->plan_type === 'free-investor' ? 'border-2 border-green-500' : '' }}">
                                    <div class="p-6">
                                        <h2 class="text-xl font-semibold text-gray-900">{{ __('I-Free') }}</h2>
                                        <p class="mt-2 text-gray-600">{{ __('Free plan for investors') }}</p>
                                        <p class="mt-4 text-3xl font-bold text-gray-900">0 zł</p>
                                        <ul class="mt-6 space-y-4">
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Browse projects') }}</p>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Express interest') }}</p>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Limited access to details') }}</p>
                                            </li>
                                        </ul>
                                        <div class="mt-8">
                                            @if(auth()->user()->plan_type === 'free-investor')
                                                <span class="w-full block py-2 px-4 bg-gray-100 text-gray-700 text-center rounded-md font-medium">
                                                    {{ __('Current plan') }}
                                                </span>
                                            @else
                                                <x-button onclick="changeToFreePlan()" class="w-full justify-center">
                                                    {{ __('Choose free plan') }}
                                                </x-button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Plan Premium dla Inwestorów -->
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ auth()->user()->plan_type === 'premium-investor' ? 'border-2 border-green-500' : 'border-2 border-indigo-500' }}">
                                    <div class="p-6">
                                        <div class="absolute top-0 right-0 bg-indigo-500 text-white px-2 py-1 text-sm rounded-bl">{{ __('Popular') }}</div>
                                        <h2 class="text-xl font-semibold text-gray-900">{{ __('I-Premium') }}</h2>
                                        <p class="mt-2 text-gray-600">{{ __('Premium plan for investors') }}</p>
                                        <p class="mt-4 text-3xl font-bold text-gray-900">500 zł<span class="text-sm text-gray-500">/{{ __('month') }}</span></p>
                                        <ul class="mt-6 space-y-4">
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Everything from I-Free plan') }}</p>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Priority access to projects') }}</p>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Advanced analytics') }}</p>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Access to exclusive projects') }}</p>
                                            </li>
                                        </ul>
                                        <div class="mt-8">
                                            @if(auth()->user()->plan_type === 'premium-investor')
                                                <span class="w-full block py-2 px-4 bg-gray-100 text-gray-700 text-center rounded-md font-medium">
                                                    {{ __('Current plan') }}
                                                </span>
                                            @else
                                                <x-button onclick="changeToPremiumInvestorPlan()" class="w-full justify-center bg-indigo-600 hover:bg-indigo-700">
                                                    {{ __('Choose plan') }}
                                                </x-button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Plan Premium dla Właścicieli -->
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ auth()->user()->plan_type === 'premium-owner' ? 'border-2 border-green-500' : '' }}">
                                    <div class="p-6">
                                        <h2 class="text-xl font-semibold text-gray-900">{{ __('O-Premium') }}</h2>
                                        <p class="mt-2 text-gray-600">{{ __('Premium plan for project owners') }}</p>
                                        <p class="mt-4 text-3xl font-bold text-gray-900">1000 zł<span class="text-sm text-gray-500">/{{ __('month') }}</span></p>
                                        <ul class="mt-6 space-y-4">
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Ability to add projects') }}</p>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Access to premium investor database') }}</p>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Interest analysis tools') }}</p>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <p class="ml-3 text-sm text-gray-700">{{ __('Support in funding process') }}</p>
                                            </li>
                                        </ul>
                                        <div class="mt-8">
                                            @if(auth()->user()->plan_type === 'premium-owner')
                                                <span class="w-full block py-2 px-4 bg-gray-100 text-gray-700 text-center rounded-md font-medium">
                                                    {{ __('Current plan') }}
                                                </span>
                                            @else
                                                <x-button onclick="changeToPremiumOwnerPlan()" class="w-full justify-center">
                                                    {{ __('Choose plan') }}
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
                                    <h2 class="text-xl font-semibold text-gray-900">{{ __('I-Free') }}</h2>
                                    <p class="mt-2 text-gray-600">{{ __('Free plan for investors') }}</p>
                                    <p class="mt-4 text-3xl font-bold text-gray-900">0 zł</p>
                                    <ul class="mt-6 space-y-4">
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Browse projects') }}</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Express interest') }}</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Limited access to details') }}</p>
                                        </li>
                                    </ul>
                                    <div class="mt-8">
                                        <x-button onclick="activateFreePlan()" class="w-full justify-center">
                                            {{ __('Activate free plan') }}
                                        </x-button>
                                    </div>
                                </div>
                            </div>

                            <!-- Plan Premium dla Inwestorów -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-indigo-500">
                                <div class="p-6">
                                    <div class="absolute top-0 right-0 bg-indigo-500 text-white px-2 py-1 text-sm rounded-bl">{{ __('Popular') }}</div>
                                    <h2 class="text-xl font-semibold text-gray-900">{{ __('I-Premium') }}</h2>
                                    <p class="mt-2 text-gray-600">{{ __('Premium plan for investors') }}</p>
                                    <p class="mt-4 text-3xl font-bold text-gray-900">500 zł<span class="text-sm text-gray-500">/{{ __('month') }}</span></p>
                                    <ul class="mt-6 space-y-4">
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Everything from I-Free plan') }}</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Priority access to projects') }}</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Advanced analytics') }}</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Access to exclusive projects') }}</p>
                                        </li>
                                    </ul>
                                    <div class="mt-8">
                                        <x-button onclick="activatePremiumInvestorPlan()" class="w-full justify-center bg-indigo-600 hover:bg-indigo-700">
                                            {{ __('Activate plan') }}
                                        </x-button>
                                    </div>
                                </div>
                            </div>

                            <!-- Plan Premium dla Właścicieli -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h2 class="text-xl font-semibold text-gray-900">{{ __('O-Premium') }}</h2>
                                    <p class="mt-2 text-gray-600">{{ __('Premium plan for project owners') }}</p>
                                    <p class="mt-4 text-3xl font-bold text-gray-900">1000 zł<span class="text-sm text-gray-500">/{{ __('month') }}</span></p>
                                    <ul class="mt-6 space-y-4">
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Ability to add projects') }}</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Access to premium investor database') }}</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Interest analysis tools') }}</p>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ __('Support in funding process') }}</p>
                                        </li>
                                    </ul>
                                    <div class="mt-8">
                                        <x-button onclick="activatePremiumOwnerPlan()" class="w-full justify-center">
                                            {{ __('Choose plan') }}
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
</x-app-layout> 