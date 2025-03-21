<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ekskluzywne projekty inwestycyjne') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 pt-1">
                        <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-purple-900">{{ __('Premium: Ekskluzywne projekty') }}</h3>
                        <p class="mt-1 text-sm text-purple-700">
                            Te ekskluzywne projekty są dostępne tylko dla użytkowników z planem premium.
                            Oferują one wyjątkowe możliwości inwestycyjne z potencjalnie wyższymi zwrotami lub specjalnymi warunkami.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filtrowanie i wyszukiwanie -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <form action="{{ route('projects.exclusive') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Wyszukaj</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Nazwa lub opis projektu">
                    </div>
                    
                    <div class="w-full md:w-1/4">
                        <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-1">Poziom ryzyka</label>
                        <select name="risk_level" id="risk_level" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option value="">Wszystkie</option>
                            <option value="low" {{ request('risk_level') === 'low' ? 'selected' : '' }}>Niskie</option>
                            <option value="medium" {{ request('risk_level') === 'medium' ? 'selected' : '' }}>Średnie</option>
                            <option value="high" {{ request('risk_level') === 'high' ? 'selected' : '' }}>Wysokie</option>
                        </select>
                    </div>
                    
                    <div class="w-full md:w-1/4">
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sortuj według</label>
                        <select name="sort_by" id="sort_by" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Najnowsze</option>
                            <option value="returns_projection" {{ request('sort_by') === 'returns_projection' ? 'selected' : '' }}>Prognozowany zwrot</option>
                            <option value="target_amount" {{ request('sort_by') === 'target_amount' ? 'selected' : '' }}>Kwota docelowa</option>
                            <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Nazwa</option>
                        </select>
                    </div>
                    
                    <div class="w-full md:w-auto md:self-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Filtruj
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lista projektów -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @if($exclusiveProjects->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        @foreach($exclusiveProjects as $project)
                            <div class="bg-white border border-gray-200 overflow-hidden rounded-lg shadow-sm hover:shadow-md transition duration-300 flex flex-col h-full">
                                <div class="p-5 flex-grow">
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-lg font-medium text-gray-900 truncate mb-2">{{ $project->name }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Ekskluzywny
                                        </span>
                                    </div>
                                    
                                    <div class="mt-2 text-sm text-gray-500 line-clamp-3">
                                        {{ Str::limit($project->description, 150) }}
                                    </div>
                                    
                                    <div class="mt-4 grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="block text-sm font-medium text-gray-500">Cel finansowania</span>
                                            <span class="block mt-1 text-lg font-semibold text-gray-900">{{ number_format($project->target_amount, 0, ',', ' ') }} PLN</span>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-medium text-gray-500">Prognozowany zwrot</span>
                                            <span class="block mt-1 text-lg font-semibold text-green-600">{{ $project->returns_projection }}%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <div class="text-sm text-gray-500 flex justify-between mb-1">
                                            <span>Postęp: {{ number_format(($project->current_amount / $project->target_amount) * 100, 0) }}%</span>
                                            <span>{{ number_format($project->current_amount, 0, ',', ' ') }} / {{ number_format($project->target_amount, 0, ',', ' ') }} PLN</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(($project->current_amount / $project->target_amount) * 100, 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 px-5 py-3 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                                            <span class="{{ $project->risk_level === 'low' ? 'text-green-600' : ($project->risk_level === 'medium' ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $project->risk_level === 'low' ? 'Niskie ryzyko' : ($project->risk_level === 'medium' ? 'Średnie ryzyko' : 'Wysokie ryzyko') }}
                                            </span>
                                            <span>•</span>
                                            <span>{{ $project->category }}</span>
                                        </div>
                                        <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            Szczegóły
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="px-6 py-4">
                        {{ $exclusiveProjects->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak ekskluzywnych projektów</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Obecnie nie ma dostępnych ekskluzywnych projektów. Sprawdź ponownie później.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 