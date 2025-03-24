<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Weryfikacja projektów') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Lista projektów do weryfikacji -->
                    <div class="space-y-6">
                        @forelse($projects as $project)
                            <div class="border rounded-lg p-6 bg-white shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">{{ $project->name }}</h3>
                                        <p class="text-sm text-gray-500">Właściciel: {{ $project->owner->name }}</p>
                                        <p class="text-sm text-gray-500">Data utworzenia: {{ $project->created_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('projects.show', $project) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                            Szczegóły
                                        </a>
                                        <form action="{{ route('projects.changeStatus', $project) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                Akceptuj
                                            </button>
                                        </form>
                                        <form action="{{ route('projects.changeStatus', $project) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                Odrzuć
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Kwota docelowa</h4>
                                        <p class="text-lg font-semibold">{{ number_format($project->target_amount, 2, ',', ' ') }} zł</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Minimalna inwestycja</h4>
                                        <p class="text-lg font-semibold">{{ number_format($project->min_investment, 2, ',', ' ') }} zł</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Kategoria</h4>
                                        <p class="text-lg font-semibold">{{ $project->category }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Lokalizacja</h4>
                                        <p class="text-lg font-semibold">{{ $project->location }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-500">Opis projektu</h4>
                                    <p class="mt-1 text-gray-900">{{ $project->description }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-gray-500">Brak projektów do weryfikacji.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Paginacja -->
                    <div class="mt-6">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 