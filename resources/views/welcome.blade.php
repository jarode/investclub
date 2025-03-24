<x-front-layout>
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-600">
        <!-- Dekoracyjne elementy tła -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/30 to-purple-600/30"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBzdHJva2U9IiNmZmYiIHN0cm9rZS13aWR0aD0iMS41IiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIG9wYWNpdHk9Ii4yIiBzdHJva2UtbGluZWNhcD0icm91bmQiPjxwYXRoIGQ9Ik0wIDBoNjBtLTYwIDEyaDYwbS02MCAyNGg2MG0tNjAgMTJoNjBtLTYwIDEyaDYwTTAgMHY2MG0xMi02MHY2MG0yNC02MHY2MG0xMi02MHY2MG0xMi02MHY2MCIvPjwvZz48L3N2Zz4=')]"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="py-16 md:py-24">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm mb-8">
                        <span class="text-white text-sm font-medium">Ekskluzywny Klub Inwestorów</span>
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl md:text-5xl">
                        Elitarna społeczność <span class="text-indigo-200">inwestorów</span>
                    </h1>
                    <p class="mt-6 max-w-lg mx-auto text-lg text-indigo-100 sm:max-w-2xl">
                        Dołącz do ekskluzywnego grona inwestorów i uzyskaj dostęp do wyselekcjonowanych projektów inwestycyjnych. Tylko dla zweryfikowanych członków.
                    </p>
                    <div class="mt-8 max-w-md mx-auto sm:flex sm:justify-center">
                        <div class="rounded-md shadow">
                            <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-gray-50 md:text-lg">
                                Aplikuj o członkostwo
                            </a>
                        </div>
                        <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                            <a href="#jak-to-dziala" class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-800 hover:bg-indigo-700 md:text-lg">
                                Poznaj zasady
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investments Preview Section -->
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Aktualne inwestycje</h2>
                <p class="mt-2 text-2xl leading-8 font-bold tracking-tight text-gray-900 sm:text-3xl">
                    Najlepsze projekty inwestycyjne
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Inwestycja 1 -->
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        <div class="relative h-48">
                            <div class="w-full h-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                                <svg class="h-16 w-16 text-white opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute top-4 left-4 bg-white px-2 py-1 rounded text-xs font-semibold text-blue-600">
                                Premium
                            </div>
                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="text-xl font-bold text-white">Apartamenty Centrum</h3>
                                <p class="text-sm text-white/90">Warszawa, Śródmieście</p>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-500">Zwrot roczny</p>
                                    <p class="font-semibold text-gray-900">8.3%</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <p class="font-semibold text-gray-900">Wynajęty</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="bg-indigo-600 h-full rounded-full" style="width: 87%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>Zebrano: 435,000 zł</span>
                                    <span>87%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inwestycja 2 -->
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        <div class="relative h-48">
                            <div class="w-full h-full bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                                <svg class="h-16 w-16 text-white opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute top-4 left-4 bg-white px-2 py-1 rounded text-xs font-semibold text-purple-600">
                                Popularny
                            </div>
                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="text-xl font-bold text-white">Marina Mokotów</h3>
                                <p class="text-sm text-white/90">Warszawa, Mokotów</p>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-500">Zwrot roczny</p>
                                    <p class="font-semibold text-gray-900">7.5%</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <p class="font-semibold text-gray-900">Wynajęty</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="bg-indigo-600 h-full rounded-full" style="width: 65%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>Zebrano: 325,000 zł</span>
                                    <span>65%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inwestycja 3 -->
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        <div class="relative h-48">
                            <div class="w-full h-full bg-gradient-to-r from-green-500 to-teal-600 flex items-center justify-center">
                                <svg class="h-16 w-16 text-white opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute top-4 left-4 bg-white px-2 py-1 rounded text-xs font-semibold text-green-600">
                                Nowy
                            </div>
                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="text-xl font-bold text-white">Apartamenty Nadmorskie</h3>
                                <p class="text-sm text-white/90">Gdańsk, Brzeźno</p>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-500">Zwrot roczny</p>
                                    <p class="font-semibold text-gray-900">8.1%</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <p class="font-semibold text-green-600">Dostępny</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="bg-indigo-600 h-full rounded-full" style="width: 42%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>Zebrano: 210,000 zł</span>
                                    <span>42%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-indigo-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-white md:text-4xl">250+</div>
                    <div class="mt-2 text-sm text-indigo-300">Zweryfikowanych członków</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-white md:text-4xl">45+</div>
                    <div class="mt-2 text-sm text-indigo-300">Wyselekcjonowanych projektów</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-white md:text-4xl">7.8%</div>
                    <div class="mt-2 text-sm text-indigo-300">Średni zwrot z inwestycji</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-white md:text-4xl">15M+</div>
                    <div class="mt-2 text-sm text-indigo-300">Łączna wartość inwestycji</div>
                </div>
            </div>
        </div>
    </div>

    <!-- How it Works Section -->
    <div id="jak-to-dziala" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Jak to działa</h2>
                <p class="mt-2 text-2xl leading-8 font-bold tracking-tight text-gray-900 sm:text-3xl">
                    LINK + INVEST = LINV
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    Łączymy inwestorów z wyselekcjonowanymi projektami w ramach ekskluzywnego klubu inwestycyjnego
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Step 1 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white">
                            <span class="text-xl font-bold">1</span>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Weryfikacja KYC</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Przejdź weryfikację tożsamości i uzyskaj dostęp do ekskluzywnego klubu inwestorów.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white">
                            <span class="text-xl font-bold">2</span>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Przeglądaj wyselekcjonowane projekty</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Poznaj szczegóły starannie wybranych projektów inwestycyjnych o wysokim potencjale.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white">
                            <span class="text-xl font-bold">3</span>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Bezpośredni kontakt</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Skontaktuj się bezpośrednio z właścicielami projektów i negocjuj warunki inwestycji.
                            </p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white">
                            <span class="text-xl font-bold">4</span>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Osiągaj zyski</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Ciesz się regularnym zwrotem z inwestycji i buduj długoterminowe bogactwo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 text-center">
                <div class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <a href="{{ route('register') }}">Dołącz do elitarnego grona</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Dlaczego LINV?</h2>
                <p class="mt-2 text-2xl leading-8 font-bold tracking-tight text-gray-900 sm:text-3xl">
                    Korzyści członkostwa
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <!-- Benefit 1 -->
                    <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Bezpieczeństwo</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Wszyscy członkowie platformy przechodzą weryfikację KYC. Projekty są sprawdzane pod kątem legalności i potencjału.
                        </p>
                    </div>

                    <!-- Benefit 2 -->
                    <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Ekskluzywna społeczność</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Dołącz do grupy doświadczonych inwestorów. Wymieniaj się wiedzą i doświadczeniami z innymi członkami klubu.
                        </p>
                    </div>

                    <!-- Benefit 3 -->
                    <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Unikalne projekty</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Dostęp do starannie wyselekcjonowanych projektów o wysokim potencjale zwrotu, niedostępnych dla szerokiej publiczności.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-indigo-700">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                <span class="block">Gotowy na nowe możliwości inwestycyjne?</span>
                <span class="block text-indigo-200">Dołącz do LINV i zacznij budować swój portfel.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                        Aplikuj teraz
                    </a>
                </div>
                <div class="ml-3 inline-flex rounded-md shadow">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-800">
                        Zaloguj się
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-front-layout>
