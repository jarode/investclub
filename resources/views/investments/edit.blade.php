<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edycja inwestycji') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('investments.show', $investment) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Powrót do szczegółów') }}
                </a>
                <a href="{{ route('investments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Powrót do listy') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Informacja o projekcie -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-md">
                        <h3 class="text-lg font-medium mb-2">{{ __('Informacje o projekcie') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Nazwa projektu') }}</p>
                                <p class="text-md font-bold">{{ $investment->project->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Kwota docelowa') }}</p>
                                <p class="text-md">{{ number_format($investment->project->target_amount, 2, ',', ' ') }} zł</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Minimalna inwestycja') }}</p>
                                <p class="text-md">{{ number_format($investment->project->min_investment, 2, ',', ' ') }} zł</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Projektowany zwrot') }}</p>
                                <p class="text-md">{{ number_format($investment->project->returns_projection, 2, ',', ' ') }}%</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Poziom ryzyka') }}</p>
                                <p class="text-md 
                                    @if($investment->project->risk_level === 'low') text-green-600
                                    @elseif($investment->project->risk_level === 'medium') text-yellow-600
                                    @elseif($investment->project->risk_level === 'high') text-red-600
                                    @endif">
                                    @if($investment->project->risk_level === 'low') {{ __('Niskie') }}
                                    @elseif($investment->project->risk_level === 'medium') {{ __('Średnie') }}
                                    @elseif($investment->project->risk_level === 'high') {{ __('Wysokie') }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Data zakończenia') }}</p>
                                <p class="text-md">{{ $investment->project->end_date->format('d.m.Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('investments.update', $investment) }}">
                        @csrf
                        @method('PUT')

                        <!-- Status inwestycji -->
                        <div class="mb-6">
                            <p class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status inwestycji') }}</p>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                @if($investment->status === 'declared') bg-yellow-100 text-yellow-800
                                @elseif($investment->status === 'paid') bg-blue-100 text-blue-800
                                @elseif($investment->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($investment->status === 'cancelled') bg-red-100 text-red-800
                                @endif">
                                @if($investment->status === 'declared') {{ __('Zadeklarowana') }}
                                @elseif($investment->status === 'paid') {{ __('Opłacona') }}
                                @elseif($investment->status === 'confirmed') {{ __('Potwierdzona') }}
                                @elseif($investment->status === 'cancelled') {{ __('Anulowana') }}
                                @endif
                            </span>
                        </div>

                        <!-- Kwota inwestycji -->
                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Kwota inwestycji (PLN)') }}</label>
                            <input type="number" step="0.01" min="{{ $investment->project->min_investment }}" id="amount" name="amount" value="{{ old('amount', $investment->amount) }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" required>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Twoje dostępne środki') }}: <span class="font-semibold">{{ number_format(auth()->user()->wallet_balance + $investment->amount, 2, ',', ' ') }} zł</span>
                                <span class="text-xs">({{ __('w tym kwota tej inwestycji') }})</span>
                            </p>
                        </div>

                        <!-- Referencja transakcji -->
                        <div class="mb-6">
                            <label for="transaction_reference" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Referencja transakcji (opcjonalnie)') }}</label>
                            <input type="text" id="transaction_reference" name="transaction_reference" value="{{ old('transaction_reference', $investment->transaction_reference) }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                            @error('transaction_reference')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Numer przelewu lub inna referencja płatności') }}
                            </p>
                        </div>

                        <!-- Uwagi -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Uwagi (opcjonalnie)') }}</label>
                            <textarea id="notes" name="notes" rows="3" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">{{ old('notes', $investment->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Podsumowanie inwestycji -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-md" id="investment-summary">
                            <h3 class="text-lg font-medium mb-2">{{ __('Podsumowanie inwestycji') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ __('Kwota inwestycji') }}</p>
                                    <p class="text-lg font-bold" id="summary-amount">{{ number_format($investment->amount, 2, ',', ' ') }} zł</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ __('Przewidywany zwrot') }}</p>
                                    <p class="text-lg font-bold text-green-600" id="summary-return">{{ number_format($investment->calculateReturnValue(), 2, ',', ' ') }} zł</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ __('Potencjalny zysk') }}</p>
                                    <p class="text-lg font-bold text-green-600" id="summary-profit">{{ number_format($investment->calculateProfit(), 2, ',', ' ') }} zł</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ __('Poziom ryzyka') }}</p>
                                    <p class="text-lg font-bold 
                                        @if($investment->project->risk_level === 'low') text-green-600
                                        @elseif($investment->project->risk_level === 'medium') text-yellow-600
                                        @elseif($investment->project->risk_level === 'high') text-red-600
                                        @endif" id="summary-risk">
                                        @if($investment->project->risk_level === 'low') {{ __('Niskie') }}
                                        @elseif($investment->project->risk_level === 'medium') {{ __('Średnie') }}
                                        @elseif($investment->project->risk_level === 'high') {{ __('Wysokie') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Przyciski -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('investments.show', $investment) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Anuluj') }}
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Aktualizuj inwestycję') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            const summaryAmount = document.getElementById('summary-amount');
            const summaryReturn = document.getElementById('summary-return');
            const summaryProfit = document.getElementById('summary-profit');
            
            // Projektowany zwrot
            const returnsProjection = {{ $investment->project->returns_projection }};
            
            // Funkcja aktualizująca podsumowanie inwestycji
            function updateInvestmentSummary() {
                const amount = parseFloat(amountInput.value) || 0;
                
                if (amount > 0) {
                    // Formatowanie liczb
                    const formatter = new Intl.NumberFormat('pl-PL', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    // Obliczenia
                    const returnValue = amount * (1 + returnsProjection / 100);
                    const profit = returnValue - amount;
                    
                    // Aktualizuj podsumowanie
                    summaryAmount.textContent = `${formatter.format(amount)} zł`;
                    summaryReturn.textContent = `${formatter.format(returnValue)} zł`;
                    summaryProfit.textContent = `${formatter.format(profit)} zł`;
                }
            }
            
            // Nasłuchiwanie zmian w kwocie inwestycji
            amountInput.addEventListener('input', updateInvestmentSummary);
        });
    </script>
    @endpush
</x-app-layout> 