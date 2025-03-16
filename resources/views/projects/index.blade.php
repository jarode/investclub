<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projekty inwestycyjne') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Filtry i wyszukiwanie -->
                <div class="mb-6">
                    <form action="{{ route('projects.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Wszystkie statusy</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Szkic</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktywny</option>
                                <option value="funded" {{ request('status') == 'funded' ? 'selected' : '' }}>Sfinansowany</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Zakończony</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="risk_level" class="block text-sm font-medium text-gray-700">Poziom ryzyka</label>
                            <select id="risk_level" name="risk_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Wszystkie poziomy ryzyka</option>
                                <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Niskie</option>
                                <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Średnie</option>
                                <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>Wysokie</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="min_investment" class="block text-sm font-medium text-gray-700">Maksymalna minimalna inwestycja</label>
                            <input type="number" name="min_investment" id="min_investment" value="{{ request('min_investment') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="np. 5000">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700">Wyszukaj</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Nazwa lub opis projektu...">
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Filtruj
                            </button>
                            <a href="{{ route('projects.index') }}" class="ml-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Resetuj
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Przyciski akcji -->
                <div class="mb-6 flex justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $projects->total() }} projektów</h3>
                    </div>
<div>
                        @can('create', App\Models\Project::class)
                            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Nowy projekt
                            </a>
                        @endcan
                    </div>
                </div>
                
                <!-- Tabela projektów -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('projects.index', array_merge(request()->except(['sort_by', 'sort_order']), ['sort_by' => 'name', 'sort_order' => request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}">
                                        Nazwa
                                        @if(request('sort_by') == 'name')
                                            @if(request('sort_order') == 'asc')
                                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('projects.index', array_merge(request()->except(['sort_by', 'sort_order']), ['sort_by' => 'target_amount', 'sort_order' => request('sort_by') == 'target_amount' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}">
                                        Kwota docelowa
                                        @if(request('sort_by') == 'target_amount')
                                            @if(request('sort_order') == 'asc')
                                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Postęp
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('projects.index', array_merge(request()->except(['sort_by', 'sort_order']), ['sort_by' => 'start_date', 'sort_order' => request('sort_by') == 'start_date' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}">
                                        Data rozpoczęcia
                                        @if(request('sort_by') == 'start_date')
                                            @if(request('sort_order') == 'asc')
                                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ryzyko
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Właściciel
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Akcje
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($projects as $project)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $project->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($project->status === 'draft') bg-gray-100 text-gray-800
                                            @elseif($project->status === 'active') bg-blue-100 text-blue-800
                                            @elseif($project->status === 'funded') bg-green-100 text-green-800
                                            @elseif($project->status === 'completed') bg-purple-100 text-purple-800
                                            @endif
                                        ">
                                            @if($project->status === 'draft') Szkic
                                            @elseif($project->status === 'active') Aktywny
                                            @elseif($project->status === 'funded') Sfinansowany
                                            @elseif($project->status === 'completed') Zakończony
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($project->target_amount, 2, ',', ' ') }} zł
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            @php $percentage = min(100, ($project->current_amount / $project->target_amount) * 100); @endphp
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <div class="text-xs mt-1">
                                            {{ number_format($project->current_amount, 2, ',', ' ') }} zł ({{ round($percentage) }}%)
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($project->start_date)->format('d.m.Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($project->risk_level === 'low') bg-green-100 text-green-800
                                            @elseif($project->risk_level === 'medium') bg-yellow-100 text-yellow-800
                                            @elseif($project->risk_level === 'high') bg-red-100 text-red-800
                                            @endif
                                        ">
                                            @if($project->risk_level === 'low') Niskie
                                            @elseif($project->risk_level === 'medium') Średnie
                                            @elseif($project->risk_level === 'high') Wysokie
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $project->owner->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            
                                            @can('update', $project)
                                                <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 hover:text-yellow-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                </a>
                                            @endcan
                                            
                                            @can('delete', $project)
                                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Czy na pewno chcesz usunąć ten projekt?')">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Nie znaleziono projektów spełniających kryteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
</div>
                
                <!-- Paginacja -->
                <div class="mt-4">
                    {{ $projects->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
