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
                <h3 class="text-lg font-medium mb-4">{{ __('Welcome to InvestClub') }}</h3>
                <p class="mb-4">
                    {{ __('Welcome message', ['name' => auth()->user()->name]) }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Status weryfikacji KYC -->
                    <div class="border rounded-lg p-4 h-full">
                        <h4 class="font-semibold mb-2">{{ __('KYC Verification Status') }}</h4>
                        
                        @if ($kycStatus === 'verified')
                            <div class="flex items-center text-green-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('Verified') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('Your identity has been successfully verified.') }}</p>
                        @elseif ($kycStatus === 'pending')
                            <div class="flex items-center text-yellow-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('Pending verification') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('Your verification is being processed.') }}</p>
                        @elseif ($kycStatus === 'requires_input')
                            <div class="flex items-center text-yellow-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('Requires additional information') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('Verification requires additional information from you.') }}</p>
                        @elseif ($kycStatus === 'canceled')
                            <div class="flex items-center text-red-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>{{ __('Canceled') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('Verification has been cancelled. Start the process again.') }}</p>
                        @elseif ($kycStatus === 'rejected')
                            <div class="flex items-center text-red-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>{{ __('Rejected') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('Verification has been rejected. Contact customer support.') }}</p>
                        @else
                            <div class="flex items-center text-red-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>{{ __('Uncompleted verification') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('Start the KYC verification process to get full access to the platform.') }}</p>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('kyc.verify') }}" class="text-indigo-600 hover:text-indigo-800">
                                @if ($kycStatus === 'verified')
                                    {{ __('View KYC status') }}
                                @elseif ($kycStatus === 'pending' || $kycStatus === 'requires_input')
                                    {{ __('View KYC status') }}
                                @else
                                    {{ __('Complete KYC verification') }}
                                @endif
                            </a>
                        </div>
                    </div>
                    
                    <!-- Status subskrypcji -->
                    <div class="border rounded-lg p-4 h-full">
                        <h4 class="font-semibold mb-2">{{ __('Subscription Status') }}</h4>
                        
                        @if (auth()->user()->hasActiveSubscription())
                            <div class="flex items-center text-green-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('Active') }}</span>
                                
                                @if (auth()->user()->hasSubscriptionPendingCancellation())
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ __('Will end in next billing period') }}
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-1">
                                <strong>{{ __('Plan') }}:</strong> 
                                @if (auth()->user()->hasPlanType('free-investor'))
                                    {{ __('I-Free (Free plan for investors)') }}
                                @elseif (auth()->user()->hasPlanType('premium-investor'))
                                    {{ __('I-Premium (Premium for investors)') }}
                                @elseif (auth()->user()->hasPlanType('premium-owner'))
                                    {{ __('O-Premium (Premium for project owners)') }}
                                @else
                                    {{ auth()->user()->plan_type }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-600">{{ __('Your subscription is active. You have access to features according to your plan.') }}</p>
                            
                            @if($nextPaymentDate && !auth()->user()->hasFreePlan())
                                <p class="text-sm text-gray-600 mt-2">
                                    <strong>{{ __('Next payment') }}:</strong> {{ $nextPaymentDate }}
                                </p>
                            @endif
                        @elseif (auth()->user()->stripe_subscription_status === 'past_due')
                            <div class="flex items-center text-red-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('Payment issue') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('There is a problem with your subscription payment. Update your payment method to maintain access to the platform.') }}</p>
                        @elseif (auth()->user()->stripe_subscription_status === 'cancelled')
                            <div class="flex items-center text-yellow-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('Cancelled') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('Your subscription has been cancelled. You can activate a new subscription to regain access to the platform.') }}</p>
                        @else
                            <div class="flex items-center text-red-600 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>{{ __('Inactive') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('You need to activate a subscription to get full access to the platform.') }}</p>
                        @endif
                        
                        <div class="mt-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('subscription') }}" class="text-indigo-600 hover:text-indigo-800 px-3 py-1 border border-indigo-600 rounded-md text-sm">
                                    @if (!auth()->user()->hasActiveSubscription())
                                        {{ __('Choose plan') }}
                                    @else
                                        {{ __('Change plan') }}
                                    @endif
                                </a>
                                
                                @if (auth()->user()->stripe_customer_id)
                                    <form action="{{ route('stripe.portal') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 px-3 py-1 border border-blue-600 rounded-md text-sm">
                                            {{ __('Stripe Payment Portal') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">{{ __('Available features') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('projects.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full {{ $kycStatus !== 'verified' || $subscriptionStatus !== 'active' ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <h4 class="font-semibold mb-2">{{ __('Investment projects') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Browse investment projects available on the platform.') }}</p>
                        @if ($kycStatus !== 'verified' || $subscriptionStatus !== 'active')
                            <div class="mt-2 text-xs text-red-600">
                                {{ __('Requires KYC verification and active subscription') }}
                            </div>
                        @endif
                    </a>
                    
                    <a href="{{ route('investments.index') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full {{ $kycStatus !== 'verified' || $subscriptionStatus !== 'active' ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <h4 class="font-semibold mb-2">{{ __('My investments') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Manage your investments and track their status.') }}</p>
                        @if ($kycStatus !== 'verified' || $subscriptionStatus !== 'active')
                            <div class="mt-2 text-xs text-red-600">
                                {{ __('Requires KYC verification and active subscription') }}
                            </div>
                        @endif
                    </a>
                    
                    <a href="{{ route('stripe.portal') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full {{ $subscriptionStatus !== 'active' ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <h4 class="font-semibold mb-2">{{ __('Payment portal') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Manage your payment methods and invoices.') }}</p>
                        @if ($subscriptionStatus !== 'active')
                            <div class="mt-2 text-xs text-red-600">
                                {{ __('Requires active subscription') }}
                            </div>
                        @endif
                    </a>
                    
                    @if (auth()->user()->isPremiumInvestor() || auth()->user()->isProjectOwner())
                    <a href="{{ route('projects.exclusive') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full bg-purple-50 border-purple-200">
                        <div class="flex items-center mb-2">
                            <h4 class="font-semibold">{{ __('Exclusive projects') }}</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Premium</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ __('Get access to exclusive projects available only to premium users.') }}</p>
                    </a>
                    @endif
                    
                    @if (auth()->user()->isProjectOwner() || auth()->user()->isAdmin())
                    <a href="{{ route('project.dashboard') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full bg-blue-50 border-blue-200">
                        <div class="flex items-center mb-2">
                            <h4 class="font-semibold">{{ __('Project owner panel') }}</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">O-Premium</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ __('Manage your projects, track investments and analyze results.') }}</p>
                    </a>
                    
                    <a href="{{ route('projects.create') }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition duration-300 h-full bg-blue-50 border-blue-200">
                        <div class="flex items-center mb-2">
                            <h4 class="font-semibold">{{ __('Create new project') }}</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">O-Premium</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ __('Add a new investment project to the platform.') }}</p>
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
                        <h3 class="text-lg font-medium mb-2">{{ __('Want to add your own projects?') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Upgrade to Owner subscription to add your own projects to our platform.') }}
                        </p>
                        <a href="{{ route('subscription') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 active:bg-blue-600 transition">
                            {{ __('Upgrade to O-Premium') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            @if($user->role === 'owner')
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">{{ __('Recent investments in your projects') }}</h2>
                    @if($recentInvestments->isEmpty())
                        <p class="text-gray-500">{{ __('No investments in your projects.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Investor') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Project') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentInvestments as $investment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $investment->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $investment->user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $investment->project->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($investment->amount, 2, ',', ' ') }} zł</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($investment->status === 'interested') bg-yellow-100 text-yellow-800
                                                    @elseif($investment->status === 'in_talks') bg-blue-100 text-blue-800
                                                    @elseif($investment->status === 'contract_signed') bg-green-100 text-green-800
                                                    @elseif($investment->status === 'cancelled') bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $investment->getStatusLabel() }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $investment->created_at->format('d.m.Y H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex space-x-2 justify-end">
                                                    <a href="{{ route('investments.show', $investment) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ __('View details') }}
                                                    </a>
                                                    @if($investment->status !== 'cancelled')
                                                        <form method="POST" action="{{ route('investments.changeStatus', $investment) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <select name="status" class="text-sm border-gray-300 rounded-md" onchange="this.form.submit()">
                                                                @foreach(\App\Models\Investment::getStatusList() as $value => $label)
                                                                    <option value="{{ $value }}" {{ $investment->status === $value ? 'selected' : '' }}>
                                                                        {{ $label }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
            
        </div>
    </div>
</x-app-layout>
