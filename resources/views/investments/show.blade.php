<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Szczegóły inwestycji') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('investments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                    {{ __('Powrót do listy') }}
                </a>
                
                @if (in_array($investment->status, ['interested', 'in_talks']))
                    <a href="{{ route('investments.edit', $investment) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                        {{ __('Edytuj') }}
                    </a>
                @endif
                
                @if (in_array($investment->status, ['interested', 'in_talks']))
                    <form method="POST" action="{{ route('investments.destroy', $investment) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm" onclick="return confirm('Czy na pewno chcesz anulować to zainteresowanie?')">
                            {{ __('Anuluj') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Status inwestycji -->
                    <div class="mb-6">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            @if($investment->status === 'interested') bg-yellow-100 text-yellow-800
                            @elseif($investment->status === 'in_talks') bg-blue-100 text-blue-800
                            @elseif($investment->status === 'contract_signed') bg-green-100 text-green-800
                            @elseif($investment->status === 'cancelled') bg-red-100 text-red-800
                            @endif">
                            @if($investment->status === 'interested') {{ __('Zainteresowany') }}
                            @elseif($investment->status === 'in_talks') {{ __('W trakcie rozmów') }}
                            @elseif($investment->status === 'contract_signed') {{ __('Umowa podpisana') }}
                            @elseif($investment->status === 'cancelled') {{ __('Anulowano') }}
                            @endif
                        </span>
                        
                        <!-- Zmiana statusu dla właściciela projektu lub admina -->
                        @can('changeStatus', $investment)
                            <div class="mt-4 p-4 border border-gray-200 rounded-md">
                                <h3 class="text-lg font-medium mb-2">{{ __('Zmień status inwestycji') }}</h3>
                                <form method="POST" action="{{ route('investments.changeStatus', $investment) }}" class="flex items-center space-x-4">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="interested" {{ $investment->status === 'interested' ? 'selected' : '' }}>{{ __('Zainteresowany') }}</option>
                                        <option value="in_talks" {{ $investment->status === 'in_talks' ? 'selected' : '' }}>{{ __('W trakcie rozmów') }}</option>
                                        <option value="contract_signed" {{ $investment->status === 'contract_signed' ? 'selected' : '' }}>{{ __('Umowa podpisana') }}</option>
                                        <option value="cancelled" {{ $investment->status === 'cancelled' ? 'selected' : '' }}>{{ __('Anulowano') }}</option>
                                    </select>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        {{ __('Zapisz zmianę') }}
                                    </button>
                                </form>
                                <p class="mt-2 text-sm text-gray-600">
                                    <strong>Uwaga:</strong> Aktualizuj status w miarę postępu rozmów z inwestorem.
                                </p>
                            </div>
                        @endcan
                    </div>
                    
                    <!-- Dane inwestycji -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Lewa kolumna -->
                        <div>
                            <h3 class="text-lg font-medium mb-4">{{ __('Dane inwestycji') }}</h3>
                            <table class="min-w-full">
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">{{ __('ID inwestycji') }}</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $investment->id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">{{ __('Inwestor') }}</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $investment->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">{{ __('Preferowana metoda kontaktu') }}</td>
                                        <td class="py-2 text-sm text-gray-900">
                                            @if($investment->contact_preference === 'email')
                                                {{ __('Email') }}
                                            @elseif($investment->contact_preference === 'phone')
                                                {{ __('Telefon') }}
                                            @elseif($investment->contact_preference === 'meeting')
                                                {{ __('Spotkanie osobiste') }}
                                            @else
                                                {{ $investment->contact_preference }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">{{ __('Dane kontaktowe') }}</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $investment->contact_details ?: 'Brak' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">{{ __('Deklarowana kwota') }}</td>
                                        <td class="py-2 text-sm text-gray-900">{{ number_format($investment->amount, 2, ',', ' ') }} zł</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">{{ __('Przewidywany zwrot') }}</td>
                                        <td class="py-2 text-sm text-gray-900">{{ number_format($investment->calculateReturnValue(), 2, ',', ' ') }} zł</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">{{ __('Potencjalny zysk') }}</td>
                                        <td class="py-2 text-sm text-gray-900">{{ number_format($investment->calculateProfit(), 2, ',', ' ') }} zł</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">{{ __('Data utworzenia') }}</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $investment->created_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm font-medium text-gray-500">{{ __('Ostatnia aktualizacja') }}</td>
                                        <td class="py-2 text-sm text-gray-900">{{ $investment->updated_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Prawa kolumna -->
                        <div>
                            <h3 class="text-lg font-medium mb-4">{{ __('Dane projektu') }}</h3>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <h4 class="text-md font-bold mb-2">
                                    <a href="{{ route('projects.show', $investment->project) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $investment->project->name }}
                                    </a>
                                </h4>
                                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($investment->project->description, 200) }}</p>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ __('Kwota docelowa') }}</p>
                                        <p class="text-sm text-gray-900">{{ number_format($investment->project->target_amount, 2, ',', ' ') }} zł</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ __('Minimalna inwestycja') }}</p>
                                        <p class="text-sm text-gray-900">{{ number_format($investment->project->min_investment, 2, ',', ' ') }} zł</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ __('Poziom ryzyka') }}</p>
                                        <p class="text-sm text-gray-900">
                                            @if($investment->project->risk_level === 'low')
                                                <span class="text-green-600">{{ __('Niskie') }}</span>
                                            @elseif($investment->project->risk_level === 'medium')
                                                <span class="text-yellow-600">{{ __('Średnie') }}</span>
                                            @elseif($investment->project->risk_level === 'high')
                                                <span class="text-red-600">{{ __('Wysokie') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ __('Projektowany zwrot') }}</p>
                                        <p class="text-sm text-gray-900">{{ number_format($investment->project->returns_projection, 2, ',', ' ') }}%</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-500">{{ __('Okres trwania projektu') }}</p>
                                    <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($investment->project->start_date)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($investment->project->end_date)->format('d.m.Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Uwagi -->
                    @if($investment->notes)
                        <div class="mt-8">
                            <h3 class="text-lg font-medium mb-2">{{ __('Uwagi') }}</h3>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <p class="text-sm text-gray-800">{{ $investment->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 