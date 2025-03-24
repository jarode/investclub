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
                        <span class="text-white text-sm font-medium">Średni zwrot inwestora w 2024: 10.1%</span>
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl md:text-5xl">
                        Buduj swój majątek poprzez <span class="text-indigo-200">inwestycje</span>
                    </h1>
                    <p class="mt-6 max-w-lg mx-auto text-lg text-indigo-100 sm:max-w-2xl">
                        Tysiące inwestorów korzysta z naszej platformy, aby uzyskać dostęp do projektów inwestycyjnych generujących przychody, już od 500 PLN.
                    </p>
                    <div class="mt-8 max-w-md mx-auto sm:flex sm:justify-center">
                        <div class="rounded-md shadow">
                            <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-gray-50 md:text-lg">
                                Rozpocznij inwestowanie
                            </a>
                        </div>
                        <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                            <a href="#jak-to-dziala" class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-800 hover:bg-indigo-700 md:text-lg">
                                Dowiedz się więcej
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
    <div class="bg-indigo-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600">10,000+</div>
                    <div class="text-sm text-gray-600">Zarejestrowanych użytkowników</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600">500+</div>
                    <div class="text-sm text-gray-600">Transakcji</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600">15+</div>
                    <div class="text-sm text-gray-600">Krajów</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600">10.1%</div>
                    <div class="text-sm text-gray-600">Średni zwrot</div>
                </div>
            </div>
        </div>
    </div>

    <!-- How it works Section -->
    <div id="jak-to-dziala" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Jak to działa</h2>
                <p class="mt-2 text-2xl leading-8 font-bold tracking-tight text-gray-900 sm:text-3xl">
                    Zbuduj dochodowy portfel inwestycyjny, łatwo
                </p>
                <p class="mt-4 max-w-2xl text-lg text-gray-500 mx-auto">
                    Dzięki naszej platformie inwestowanie w nieruchomości i inne projekty jest proste i dostępne dla każdego
                </p>
            </div>

            <div class="mt-12">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Step 1 -->
                    <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-500 text-white">
                                    <span class="text-xl font-bold">1</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Przeglądaj</h3>
                            </div>
                        </div>
                        <p class="mt-3 text-base text-gray-500">
                            Uzyskaj dostęp do najlepszych projektów inwestycyjnych wybranych przez ekspertów
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-500 text-white">
                                    <span class="text-xl font-bold">2</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Inwestuj</h3>
                            </div>
                        </div>
                        <p class="mt-3 text-base text-gray-500">
                            Wybierz projekty, które Ci się podobają i zainwestuj już od 500 PLN
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-500 text-white">
                                    <span class="text-xl font-bold">3</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Zarabiaj</h3>
                            </div>
                        </div>
                        <p class="mt-3 text-base text-gray-500">
                            Ciesz się regularnym pasywnym dochodem bez wysiłku
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-500 text-white">
                                    <span class="text-xl font-bold">4</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Sprzedawaj</h3>
                            </div>
                        </div>
                        <p class="mt-3 text-base text-gray-500">
                            Skorzystaj z płynności, kiedy jej potrzebujesz
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Opinie inwestorów</h2>
                <p class="mt-2 text-2xl leading-8 font-bold tracking-tight text-gray-900 sm:text-3xl">
                    Co mówią nasi klienci
                </p>
            </div>
            
            <div class="mt-10">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Testimonial 1 -->
                    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                    AM
                                </div>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-base font-medium text-gray-900">Adam Malinowski</h4>
                                <p class="text-sm text-gray-500">Inwestor od 2023</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 italic">
                            "Platforma jest bardzo intuicyjna i łatwa w obsłudze. Mogę śledzić wszystkie moje inwestycje w jednym miejscu. Wsparcie zespołu jest zawsze pomocne."
                        </p>
                        <div class="mt-4 flex text-yellow-400">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold">
                                    KW
                                </div>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-base font-medium text-gray-900">Katarzyna Wiśniewska</h4>
                                <p class="text-sm text-gray-500">Inwestorka od 2022</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 italic">
                            "Jedna z najlepszych opcji inwestowania w nieruchomości. Wystarczy zacząć od 500 zł. To proste i równie satysfakcjonujące przy minimalnym wysiłku."
                        </p>
                        <div class="mt-4 flex text-yellow-400">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-green-600 flex items-center justify-center text-white font-bold">
                                    MK
                                </div>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-base font-medium text-gray-900">Michał Kowalski</h4>
                                <p class="text-sm text-gray-500">Inwestor od 2021</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 italic">
                            "Płynność w nieruchomościach jest czasami trudna, ale dzięki okresowym oknom wyjścia, życie staje się łatwiejsze, jeśli chcesz sprzedać swoje udziały."
                        </p>
                        <div class="mt-4 flex text-yellow-400">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-indigo-700">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:py-12 sm:px-6 lg:px-8">
            <div class="lg:flex lg:items-center lg:justify-between">
                <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                    <span class="block">Gotowy, aby zacząć?</span>
                    <span class="block text-indigo-200 text-xl mt-1">Dołącz do społeczności inwestorów już dziś.</span>
                </h2>
                <div class="mt-8 flex flex-shrink-0 lg:mt-0">
                    <div class="inline-flex rounded-md shadow">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                            Zarejestruj się
                        </a>
                    </div>
                    <div class="ml-3 inline-flex rounded-md shadow">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Zaloguj się
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-front-layout>
