<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('KYC Verification') }}
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
                    <h3 class="text-lg font-medium mb-2">{{ __('KYC Verification Status') }}</h3>
                    
                    @if ($kycStatus === 'verified')
                        <div class="flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ __('KYC verification completed successfully') }}</span>
                        </div>
                        <p class="mt-2 text-gray-600">{{ __('You have full access to the InvestClub platform.') }}</p>
                    @elseif ($kycStatus === 'pending')
                        <div class="flex items-center text-yellow-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ __('KYC verification in progress') }}</span>
                        </div>
                        <p class="mt-2 text-gray-600">{{ __('Your verification is being processed. The process may take up to 24 hours.') }}</p>
                    @elseif ($kycStatus === 'requires_input')
                        <div class="flex items-center text-yellow-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ __('KYC verification requires additional information') }}</span>
                        </div>
                        <p class="mt-2 text-gray-600">{{ __('To complete the verification process, we need additional information. Please try again.') }}</p>
                    @elseif ($kycStatus === 'canceled')
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>{{ __('KYC verification has been canceled') }}</span>
                        </div>
                        <p class="mt-2 text-gray-600">{{ __('The verification was canceled or interrupted. Please start the verification process again.') }}</p>
                    @elseif ($kycStatus === 'rejected')
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>{{ __('KYC verification has been rejected') }}</span>
                        </div>
                        <p class="mt-2 text-gray-600">{{ __('Unfortunately, your verification has been rejected. Please contact customer support for more information.') }}</p>
                    @else
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>{{ __('KYC verification not performed') }}</span>
                        </div>
                        <p class="mt-2 text-gray-600">{{ __('To gain full access to the InvestClub platform, you must complete KYC verification.') }}</p>
                    @endif
                </div>

                @if ($kycStatus !== 'verified')
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">{{ __('Complete KYC verification') }}</h3>
                        <p class="mb-4 text-gray-600">
                            {{ __('KYC (Know Your Customer) verification is required by law and helps us ensure security for all participants. You will be redirected to a secure verification process operated by Stripe.') }}
                        </p>
                        
                        @if ($kycStatus !== 'pending' && $kycStatus !== 'requires_input')
                        <form action="{{ route('kyc.start') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
                                {{ __('Start KYC verification') }}
                            </button>
                        </form>
                        @else
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <p class="text-yellow-700">
                                @if ($kycStatus === 'pending')
                                    {{ __('Your verification is currently being processed. Please be patient.') }}
                                @else
                                    {{ __('Your verification requires additional information. Please contact customer support.') }}
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                @endif

                <div>
                    <h3 class="text-lg font-medium mb-2">{{ __('Why KYC verification is important') }}</h3>
                    <p class="text-gray-600">
                        {{ __('KYC verification is an essential security element in the investment industry. It helps us:') }}
                    </p>
                    <ul class="mt-2 space-y-1 list-disc list-inside text-gray-600">
                        <li>{{ __('Prevent fraud and money laundering') }}</li>
                        <li>{{ __('Meet legal and regulatory requirements') }}</li>
                        <li>{{ __('Increase security and trust for all platform participants') }}</li>
                        <li>{{ __('Ensure the highest standards of transaction security') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 