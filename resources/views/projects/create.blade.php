<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Utwórz nowy projekt inwestycyjny') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Nazwa projektu -->
                        <div>
                            <x-label for="name" value="{{ __('Nazwa projektu') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Opis projektu -->
                        <div>
                            <x-label for="description" value="{{ __('Opis projektu') }}" />
                            <textarea id="description" name="description" rows="6" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Kwota docelowa i minimalna inwestycja -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="target_amount" value="{{ __('Kwota docelowa (zł)') }}" />
                                <x-input id="target_amount" class="block mt-1 w-full" type="number" name="target_amount" :value="old('target_amount')" min="1000" step="1000" required />
                                @error('target_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <x-label for="min_investment" value="{{ __('Minimalna inwestycja (zł)') }}" />
                                <x-input id="min_investment" class="block mt-1 w-full" type="number" name="min_investment" :value="old('min_investment')" min="100" step="100" required />
                                @error('min_investment')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Daty rozpoczęcia i zakończenia -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="start_date" value="{{ __('Data rozpoczęcia') }}" />
                                <x-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                                @error('start_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <x-label for="end_date" value="{{ __('Data zakończenia') }}" />
                                <x-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" required />
                                @error('end_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Prognozowany zwrot i poziom ryzyka -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="returns_projection" value="{{ __('Prognozowany zwrot (%)') }}" />
                                <x-input id="returns_projection" class="block mt-1 w-full" type="number" name="returns_projection" :value="old('returns_projection')" min="0" max="100" step="0.1" required />
                                @error('returns_projection')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <x-label for="risk_level" value="{{ __('Poziom ryzyka') }}" />
                                <select id="risk_level" name="risk_level" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="">Wybierz poziom ryzyka</option>
                                    <option value="low" {{ old('risk_level') == 'low' ? 'selected' : '' }}>Niskie</option>
                                    <option value="medium" {{ old('risk_level') == 'medium' ? 'selected' : '' }}>Średnie</option>
                                    <option value="high" {{ old('risk_level') == 'high' ? 'selected' : '' }}>Wysokie</option>
                                </select>
                                @error('risk_level')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Przyciski akcji -->
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mr-2">
                                {{ __('Anuluj') }}
                            </a>
                            
                            <x-button>
                                {{ __('Utwórz projekt') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
