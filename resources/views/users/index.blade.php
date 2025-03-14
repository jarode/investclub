<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista użytkowników') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                
                @can('create', App\Models\User::class)
                    <div class="mb-4">
                        <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Dodaj użytkownika
                        </a>
                    </div>
                @endcan
                
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-medium text-gray-500 uppercase tracking-wider">Imię</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-medium text-gray-500 uppercase tracking-wider">Rola</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $user->role }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $user->verification_status }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    @can('view', $user)
                                        <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900">Podgląd</a>
                                    @endcan
                                    
                                    @can('update', $user)
                                        <a href="{{ route('users.edit', $user) }}" class="ml-4 text-green-600 hover:text-green-900">Edytuj</a>
                                    @endcan
                                    
                                    @can('delete', $user)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-4 text-red-600 hover:text-red-900">Usuń</button>
                                        </form>
                                    @endcan
                                    
                                    @can('manageInvestments', $user)
                                        <a href="{{ route('users.investments', $user) }}" class="ml-4 text-purple-600 hover:text-purple-900">Inwestycje</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
