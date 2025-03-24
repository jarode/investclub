<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nowa inwestycja') }}
            </h2>
            <a href="{{ route('investments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Powrót do listy') }}
            </a>
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

                    <form method="POST" action="{{ route('investments.store') }}">
                        @csrf

                        <!-- Projekt -->
                        <div class="mb-6">
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Projekt') }}</label>
                            <select id="project_id" name="project_id" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" required>
                                <option value="">{{ __('Wybierz projekt') }}</option>
                                @if(isset($availableProjects) && $availableProjects)
                                    @foreach ($availableProjects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id || (isset($selectedProject) && $selectedProject->id == $project->id) ? 'selected' : '' }}>
                                            {{ $project->name }} - {{ number_format($project->target_amount, 2, ',', ' ') }} zł ({{ __('Min.') }} {{ number_format($project->min_investment, 2, ',', ' ') }} zł)
                                        </option>
                                    @endforeach
                                @elseif(isset($project))
                                    <option value="{{ $project->id }}" selected>
                                        {{ $project->name }} - {{ number_format($project->target_amount, 2, ',', ' ') }} zł ({{ __('Min.') }} {{ number_format($project->min_investment, 2, ',', ' ') }} zł)
                                    </option>
                                @endif
                            </select>
                            @error('project_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kwota inwestycji -->
                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Kwota inwestycji (PLN)') }}</label>
                            <input type="number" step="0.01" min="0" id="amount" name="amount" value="{{ old('amount') }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" required>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Twoje dostępne środki') }}: <span class="font-semibold">{{ number_format(auth()->user()->wallet_balance, 2, ',', ' ') }} zł</span>
                            </p>
                        </div>

                        <!-- Preferowana metoda kontaktu -->
                        <div class="mb-6">
                            <label for="contact_preference" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Preferowana metoda kontaktu') }}</label>
                            <select id="contact_preference" name="contact_preference" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" required>
                                <option value="">{{ __('Wybierz metodę kontaktu') }}</option>
                                <option value="email" {{ old('contact_preference') == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                                <option value="phone" {{ old('contact_preference') == 'phone' ? 'selected' : '' }}>{{ __('Telefon') }}</option>
                            </select>
                            @error('contact_preference')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dane kontaktowe -->
                        <div class="mb-6">
                            <label for="contact_details" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Dane kontaktowe') }}</label>
                            <input type="text" id="contact_details" name="contact_details" value="{{ old('contact_details') }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full" required>
                            @error('contact_details')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Podaj swój adres email lub numer telefonu w zależności od wybranej metody kontaktu') }}
                            </p>
                        </div>

                        <!-- Referencja transakcji -->
                        <div class="mb-6">
                            <label for="transaction_reference" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Referencja transakcji (opcjonalnie)') }}</label>
                            <input type="text" id="transaction_reference" name="transaction_reference" value="{{ old('transaction_reference') }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
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
                            <textarea id="notes" name="notes" rows="3" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Informacje o projekcie -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-md hidden" id="project-info">
                            <h3 class="text-lg font-medium mb-2">{{ __('Informacje o projekcie') }}</h3>
                            <div id="project-details" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Dane projektu będą wstawione przez JavaScript -->
                            </div>
                        </div>

                        <!-- Podsumowanie inwestycji -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-md hidden" id="investment-summary">
                            <h3 class="text-lg font-medium mb-2">{{ __('Podsumowanie inwestycji') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ __('Kwota inwestycji') }}</p>
                                    <p class="text-lg font-bold" id="summary-amount">0,00 zł</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ __('Przewidywany zwrot') }}</p>
                                    <p class="text-lg font-bold text-green-600" id="summary-return">0,00 zł</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ __('Potencjalny zysk') }}</p>
                                    <p class="text-lg font-bold text-green-600" id="summary-profit">0,00 zł</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ __('Poziom ryzyka') }}</p>
                                    <p class="text-lg font-bold" id="summary-risk">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- Przyciski -->
                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Zadeklaruj inwestycję') }}
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
            const projectSelect = document.getElementById('project_id');
            const amountInput = document.getElementById('amount');
            const projectInfo = document.getElementById('project-info');
            const projectDetails = document.getElementById('project-details');
            const investmentSummary = document.getElementById('investment-summary');
            const summaryAmount = document.getElementById('summary-amount');
            const summaryReturn = document.getElementById('summary-return');
            const summaryProfit = document.getElementById('summary-profit');
            const summaryRisk = document.getElementById('summary-risk');
            
            // Projekty jako JSON
            @if(isset($availableProjects) && $availableProjects)
                const projects = @json($availableProjects);
            @elseif(isset($project))
                const projects = [@json($project)];
            @else
                const projects = [];
            @endif
            
            // Funkcja aktualizująca informacje o projekcie
            function updateProjectInfo() {
                const projectId = parseInt(projectSelect.value);
                
                if (projectId) {
                    const project = projects.find(p => p.id === projectId);
                    
                    if (project) {
                        // Wyświetl informacje o projekcie
                        projectInfo.classList.remove('hidden');
                        
                        // Formatowanie liczb
                        const formatter = new Intl.NumberFormat('pl-PL', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        
                        // Poziom ryzyka
                        let riskLevel = '';
                        let riskColor = '';
                        
                        if (project.risk_level === 'low') {
                            riskLevel = 'Niskie';
                            riskColor = 'text-green-600';
                        } else if (project.risk_level === 'medium') {
                            riskLevel = 'Średnie';
                            riskColor = 'text-yellow-600';
                        } else if (project.risk_level === 'high') {
                            riskLevel = 'Wysokie';
                            riskColor = 'text-red-600';
                        }
                        
                        // Aktualizuj szczegóły projektu
                        projectDetails.innerHTML = `
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Nazwa projektu') }}</p>
                                <p class="text-md font-bold">${project.name}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Kwota docelowa') }}</p>
                                <p class="text-md">${formatter.format(project.target_amount)} zł</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Minimalna inwestycja') }}</p>
                                <p class="text-md">${formatter.format(project.min_investment)} zł</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Projektowany zwrot') }}</p>
                                <p class="text-md">${formatter.format(project.returns_projection)}%</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Poziom ryzyka') }}</p>
                                <p class="text-md ${riskColor}">${riskLevel}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ __('Data zakończenia') }}</p>
                                <p class="text-md">${project.end_date}</p>
                            </div>
                        `;
                        
                        // Ustaw minimalną kwotę inwestycji
                        amountInput.min = project.min_investment;
                        
                        // Aktualizuj podsumowanie
                        updateInvestmentSummary(project);
                    }
                } else {
                    projectInfo.classList.add('hidden');
                    investmentSummary.classList.add('hidden');
                }
            }
            
            // Funkcja aktualizująca podsumowanie inwestycji
            function updateInvestmentSummary(project) {
                const amount = parseFloat(amountInput.value) || 0;
                
                if (amount > 0 && project) {
                    // Wyświetl podsumowanie
                    investmentSummary.classList.remove('hidden');
                    
                    // Formatowanie liczb
                    const formatter = new Intl.NumberFormat('pl-PL', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    // Obliczenia
                    const returnValue = amount * (1 + project.returns_projection / 100);
                    const profit = returnValue - amount;
                    
                    // Aktualizuj podsumowanie
                    summaryAmount.textContent = `${formatter.format(amount)} zł`;
                    summaryReturn.textContent = `${formatter.format(returnValue)} zł`;
                    summaryProfit.textContent = `${formatter.format(profit)} zł`;
                    
                    // Poziom ryzyka
                    let riskLevel = '';
                    let riskColor = '';
                    
                    if (project.risk_level === 'low') {
                        riskLevel = 'Niskie';
                        riskColor = 'text-green-600';
                    } else if (project.risk_level === 'medium') {
                        riskLevel = 'Średnie';
                        riskColor = 'text-yellow-600';
                    } else if (project.risk_level === 'high') {
                        riskLevel = 'Wysokie';
                        riskColor = 'text-red-600';
                    }
                    
                    summaryRisk.textContent = riskLevel;
                    summaryRisk.className = `text-lg font-bold ${riskColor}`;
                } else {
                    investmentSummary.classList.add('hidden');
                }
            }
            
            // Nasłuchiwanie zmian w wyborze projektu
            projectSelect.addEventListener('change', updateProjectInfo);
            
            // Nasłuchiwanie zmian w kwocie inwestycji
            amountInput.addEventListener('input', function() {
                const projectId = parseInt(projectSelect.value);
                if (projectId) {
                    const project = projects.find(p => p.id === projectId);
                    if (project) {
                        updateInvestmentSummary(project);
                    }
                }
            });
            
            // Inicjalizacja
            if (projectSelect.value) {
                updateProjectInfo();
            }
        });
    </script>
    @endpush
</x-app-layout> 