<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Moje inwestycje') }}
            </h2>
            <a href="{{ route('investments.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Nowa inwestycja') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Filtry -->
                    <div class="bg-gray-50 p-4 rounded-md mb-6">
                        <h3 class="text-lg font-medium mb-2">{{ __('Filtry') }}</h3>
                        <form method="GET" action="{{ route('investments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                                <select id="status" name="status" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                                    <option value="">{{ __('Wszystkie') }}</option>
                                    @foreach(\App\Models\Investment::getStatusList() as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Projekt -->
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Projekt') }}</label>
                                <select id="project_id" name="project_id" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                                    <option value="">{{ __('Wszystkie') }}</option>
                                    @foreach(\App\Models\Project::orderBy('name')->get() as $project)
                                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sortowanie -->
                            <div>
                                <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Sortuj według') }}</label>
                                <select id="sort_by" name="sort_by" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>{{ __('Data utworzenia') }}</option>
                                    <option value="amount" {{ request('sort_by') == 'amount' ? 'selected' : '' }}>{{ __('Kwota') }}</option>
                                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>{{ __('Status') }}</option>
                                </select>
                            </div>

                            <!-- Kierunek sortowania -->
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Kierunek') }}</label>
                                <select id="sort_order" name="sort_order" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>{{ __('Malejąco') }}</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>{{ __('Rosnąco') }}</option>
                                </select>
                            </div>

                            <!-- Przyciski -->
                            <div class="md:col-span-4 flex items-center justify-end space-x-4">
                                <a href="{{ route('investments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Wyczyść') }}
                                </a>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Filtruj') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista inwestycji -->
                    @if($investments->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">{{ __('Nie masz jeszcze żadnych inwestycji.') }}</p>
                            <a href="{{ route('projects.index') }}" class="mt-4 inline-block text-blue-500 hover:text-blue-700">
                                {{ __('Przeglądaj dostępne projekty') }}
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Projekt') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Kwota') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Data') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Akcje') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($investments as $investment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <a href="{{ route('projects.show', $investment->project) }}" class="hover:text-blue-500">
                                                                {{ $investment->project->name }}
                                                            </a>
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ \Illuminate\Support\Str::limit($investment->project->description, 50) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($investment->amount, 2, ',', ' ') }} zł</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ __('Zwrot') }}: {{ number_format($investment->calculateReturnValue(), 2, ',', ' ') }} zł
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $investment->created_at->format('d.m.Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $investment->created_at->format('H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex space-x-2 justify-end">
                                                    <a href="{{ route('investments.show', $investment) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    
                                                    @if($investment->status === 'declared')
                                                        <a href="{{ route('investments.edit', $investment) }}" class="text-yellow-600 hover:text-yellow-900">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    
                                                    @if(in_array($investment->status, ['declared', 'paid']))
                                                        <form method="POST" action="{{ route('investments.destroy', $investment) }}" class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Czy na pewno chcesz anulować tę inwestycję?')">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginacja -->
                        <div class="mt-4">
                            {{ $investments->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 