<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $project->name }}
            </h2>
            <div class="flex items-center space-x-2">
                @if($project->status === 'draft')
                    @can('changeStatus', $project)
                        <form action="{{ route('projects.changeStatus', $project) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="active">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Publikuj projekt
                            </button>
                        </form>
                    @endcan
                @endif
                
                @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Edytuj projekt
                    </a>
                @endcan
                
                @can('delete', $project)
                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm" onclick="return confirm('Czy na pewno chcesz usunąć ten projekt?')">
                            Usuń projekt
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Status i dane ogólne -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
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
                            <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                @if($project->risk_level === 'low') bg-green-100 text-green-800
                                @elseif($project->risk_level === 'medium') bg-yellow-100 text-yellow-800
                                @elseif($project->risk_level === 'high') bg-red-100 text-red-800
                                @endif
                            ">
                                Ryzyko: 
                                @if($project->risk_level === 'low') Niskie
                                @elseif($project->risk_level === 'medium') Średnie
                                @elseif($project->risk_level === 'high') Wysokie
                                @endif
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Utworzono: {{ $project->created_at->format('d.m.Y H:i') }}</p>
                            <p class="text-sm text-gray-500">Ostatnia aktualizacja: {{ $project->updated_at->format('d.m.Y H:i') }}</p>
                            <p class="text-sm text-gray-500">Właściciel: {{ $project->owner->name }}</p>
                        </div>
                    </div>
                    
                    <!-- Postęp finansowania -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Finansowanie</h3>
                        <div class="flex items-end justify-between mb-2">
                            <div>
                                <p class="text-sm text-gray-500">Cel: <span class="font-bold">{{ number_format($project->target_amount, 2, ',', ' ') }} zł</span></p>
                                <p class="text-sm text-gray-500">Zebrano: <span class="font-bold">{{ number_format($project->current_amount, 2, ',', ' ') }} zł</span></p>
                            </div>
                            <div>
                                @php $percentage = min(100, ($project->current_amount / $project->target_amount) * 100); @endphp
                                <p class="text-2xl font-bold text-indigo-600">{{ number_format($percentage, 1, ',', ' ') }}%</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-indigo-600 h-4 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Detale czasowe -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500">Data rozpoczęcia</h4>
                            <p class="text-lg font-semibold">{{ \Carbon\Carbon::parse($project->start_date)->format('d.m.Y') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500">Data zakończenia</h4>
                            <p class="text-lg font-semibold">{{ \Carbon\Carbon::parse($project->end_date)->format('d.m.Y') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500">Minimalna inwestycja</h4>
                            <p class="text-lg font-semibold">{{ number_format($project->min_investment, 2, ',', ' ') }} zł</p>
                        </div>
                    </div>
                    
                    <!-- Kategoria i lokalizacja -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500">Kategoria</h4>
                            <p class="text-lg font-semibold">{{ $project->category }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500">Lokalizacja</h4>
                            <p class="text-lg font-semibold">{{ $project->location }}</p>
                        </div>
                    </div>
                    
                    <!-- Prognoza zwrotu -->
                    <div class="mb-6 p-4 bg-indigo-50 rounded-lg">
                        <h3 class="text-lg font-medium text-indigo-900 mb-2">Prognozowany zwrot</h3>
                        <div class="flex items-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ $project->returns_projection }}%</div>
                            <div class="ml-4 text-sm text-gray-500">
                                <p>Przy minimalnej inwestycji: {{ number_format($project->min_investment * ($project->returns_projection / 100), 2, ',', ' ') }} zł</p>
                                <p>Szacowany całkowity zwrot: {{ number_format($project->target_amount * ($project->returns_projection / 100), 2, ',', ' ') }} zł</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Opis projektu -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Opis projektu</h3>
                        <div class="prose max-w-none">
                            {{ $project->description }}
                        </div>
                    </div>
                    
                    @if($project->status === 'active')
                        <!-- Panel inwestycyjny (tylko dla aktywnych projektów) -->
                        <div class="mt-8 p-6 bg-green-50 rounded-lg border border-green-200">
                            <h3 class="text-xl font-bold text-green-800 mb-4">Wyraź zainteresowanie tym projektem</h3>
                            
                            @if(auth()->user()->kyc_status === 'verified' && auth()->user()->hasActiveSubscription())
                                <form action="{{ route('investments.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700">Kwota potencjalnej inwestycji (min. {{ number_format($project->min_investment, 2, ',', ' ') }} zł)</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <input type="number" name="amount" id="amount" min="{{ $project->min_investment }}" step="100" value="{{ $project->min_investment }}"
                                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
                                            <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                zł
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="contact_preference" class="block text-sm font-medium text-gray-700">Preferowana metoda kontaktu</label>
                                        <select name="contact_preference" id="contact_preference" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="email">Email</option>
                                            <option value="phone">Telefon</option>
                                            <option value="meeting">Spotkanie osobiste</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="contact_details" class="block text-sm font-medium text-gray-700">Dane kontaktowe</label>
                                        <input type="text" name="contact_details" id="contact_details" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               placeholder="Adres email lub numer telefonu">
                                    </div>
                                    
                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700">Uwagi (opcjonalnie)</label>
                                        <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input id="agreement" name="agreement" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" required>
                                        <label for="agreement" class="ml-2 block text-sm text-gray-900">
                                            Wyrażam zgodę na kontakt ze strony właściciela projektu i akceptuję regulamin platformy.
                                        </label>
                                    </div>
                                    
                                    <div>
                                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Wyślij zgłoszenie
                                        </button>
                                    </div>
                                </form>
                            @elseif(auth()->user()->kyc_status !== 'verified')
                                <div class="bg-yellow-100 p-4 rounded-md mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">Weryfikacja KYC wymagana</h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>Aby wyrazić zainteresowanie projektem, musisz najpierw zweryfikować swoją tożsamość. Przejdź do ustawień profilu, aby ukończyć proces weryfikacji KYC.</p>
                                            </div>
                                            <div class="mt-4">
                                                <div class="-mx-2 -my-1.5 flex">
                                                    <a href="{{ route('kyc.verify') }}" class="bg-yellow-200 px-2 py-1.5 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600">
                                                        Przejdź do weryfikacji
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif(!auth()->user()->hasActiveSubscription())
                                <div class="bg-yellow-100 p-4 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">Aktywna subskrypcja wymagana</h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>Aby wyrazić zainteresowanie projektami, musisz posiadać aktywną subskrypcję. Przejdź do ustawień subskrypcji, aby wybrać odpowiedni plan.</p>
                                            </div>
                                            <div class="mt-4">
                                                <div class="-mx-2 -my-1.5 flex">
                                                    <a href="{{ route('subscription') }}" class="bg-yellow-200 px-2 py-1.5 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600">
                                                        Wybierz plan subskrypcji
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Lista inwestycji w projekcie -->
                    @php
                        $isAdmin = auth()->user()->isAdmin();
                        $isProjectOwner = auth()->user()->id === $project->owner_id;
                        $userInvestments = $project->investments->where('user_id', auth()->id());
                        
                        // Ustal, które inwestycje pokazać
                        $investmentsToShow = $isAdmin || $isProjectOwner 
                            ? $project->investments 
                            : $userInvestments;
                    @endphp
                    
                    @if($investmentsToShow->isNotEmpty())
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                @if($isAdmin || $isProjectOwner)
                                    Inwestycje w projekcie
                                @else
                                    Twoje inwestycje w tym projekcie
                                @endif
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inwestor</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kwota</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                            @if(auth()->user()->id === $project->owner_id)
                                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($investmentsToShow as $investment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $investment->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $investment->user->email }}</div>
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
                                                @if(auth()->user()->id === $project->owner_id)
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <div class="flex space-x-2 justify-end">
                                                            <a href="{{ route('investments.show', $investment) }}" class="text-indigo-600 hover:text-indigo-900">
                                                                Szczegóły
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
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @elseif(auth()->user()->id === $project->owner_id && $project->investments->isEmpty())
                        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-600 text-center">Ten projekt nie ma jeszcze żadnych inwestycji.</p>
                        </div>
                    @elseif(!$isAdmin && !$isProjectOwner && $userInvestments->isEmpty())
                        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-600 text-center">Nie masz jeszcze żadnych inwestycji w tym projekcie.</p>
                        </div>
                    @endif
                    
                    <!-- Zarządzanie statusem projektu (tylko dla administratorów) -->
                    @can('changeStatus', $project)
                        <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Zmień status projektu</h3>
                            <form action="{{ route('projects.changeStatus', $project) }}" method="POST" class="flex space-x-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="draft" {{ $project->status === 'draft' ? 'selected' : '' }}>Szkic</option>
                                    <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Aktywny</option>
                                    <option value="funded" {{ $project->status === 'funded' ? 'selected' : '' }}>Sfinansowany</option>
                                    <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Zakończony</option>
                                </select>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Aktualizuj status
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
