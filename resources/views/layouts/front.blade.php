<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script src="//unpkg.com/alpinejs" defer></script>

        <!-- Skrypt do obsługi zmiany języka -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Sprawdzamy, czy URL zawiera parametr _nocache (oznacza to zmianę języka)
                if (window.location.href.includes('_nocache=')) {
                    console.log('Wykryto zmianę języka, odświeżam stronę...');
                    
                    // Usuwamy parametr _nocache z URL bez przeładowania strony
                    const url = new URL(window.location.href);
                    url.searchParams.delete('_nocache');
                    window.history.replaceState({}, document.title, url.toString());
                    
                    // Wymuszamy pełne odświeżenie strony po małym opóźnieniu
                    setTimeout(function() {
                        window.location.reload(true);
                    }, 100);
                }
            });
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('welcome') }}">
                                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <x-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')">
                                    {{ __('Home') }}
                                </x-nav-link>
                                <x-nav-link href="#jak-to-dziala">
                                    {{ __('How it works') }}
                                </x-nav-link>
                                <x-nav-link href="#korzyści">
                                    {{ __('Benefits') }}
                                </x-nav-link>
                                <x-nav-link href="#inwestycje">
                                    {{ __('Investments') }}
                                </x-nav-link>
                            </div>
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Language Switcher -->
                            <div class="mr-4">
                                @include('partials.language_switcher')
                            </div>
                            
                            @auth
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            <div>{{ Auth::user()->name }}</div>

                                            <div class="ml-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('dashboard')">
                                            {{ __('Dashboard') }}
                                        </x-dropdown-link>

                                        <!-- Authentication -->
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf

                                            <x-dropdown-link :href="route('logout')"
                                                    onclick="event.preventDefault();
                                                                this.closest('form').submit();">
                                                {{ __('Log Out') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @else
                                <div class="space-x-4">
                                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">{{ __('Login') }}</a>
                                    <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:text-gray-900">{{ __('Register') }}</a>
                                </div>
                            @endauth
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                    <!-- Language Switcher - Mobile -->
                    <div class="pt-2 pb-1 px-4">
                        <div class="text-xs text-gray-400 mb-1">{{ __('Select language') }}</div>
                        <div class="flex space-x-2">
                            <a href="{{ route('language.switch', 'pl') }}" class="flex items-center px-2 py-1 rounded @if(app()->getLocale() == 'pl') bg-gray-200 @endif">
                                <img src="{{ asset('img/flags/pl.png') }}" alt="🇵🇱" class="h-4 w-5 mr-1" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;12&quot;><rect width=&quot;20&quot; height=&quot;6&quot; fill=&quot;white&quot;/><rect width=&quot;20&quot; height=&quot;6&quot; y=&quot;6&quot; fill=&quot;red&quot;/></svg>';" />
                                <span class="text-sm">PL</span>
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="flex items-center px-2 py-1 rounded @if(app()->getLocale() == 'en') bg-gray-200 @endif">
                                <img src="{{ asset('img/flags/gb.png') }}" alt="🇬🇧" class="h-4 w-5 mr-1" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;12&quot; viewBox=&quot;0 0 60 30&quot;><clipPath id=&quot;s&quot;><path d=&quot;M0,0 v30 h60 v-30 z&quot;/></clipPath><clipPath id=&quot;t&quot;><path d=&quot;M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z&quot;/></clipPath><g clip-path=&quot;url(#s)&quot;><path d=&quot;M0,0 v30 h60 v-30 z&quot; fill=&quot;#012169&quot;/><path d=&quot;M0,0 L60,30 M60,0 L0,30&quot; stroke=&quot;#fff&quot; stroke-width=&quot;6&quot;/><path d=&quot;M0,0 L60,30 M60,0 L0,30&quot; clip-path=&quot;url(#t)&quot; stroke=&quot;#C8102E&quot; stroke-width=&quot;4&quot;/><path d=&quot;M30,0 v30 M0,15 h60&quot; stroke=&quot;#fff&quot; stroke-width=&quot;10&quot;/><path d=&quot;M30,0 v30 M0,15 h60&quot; stroke=&quot;#C8102E&quot; stroke-width=&quot;6&quot;/></g></svg>';" />
                                <span class="text-sm">EN</span>
                            </a>
                            <a href="{{ route('language.switch', 'de') }}" class="flex items-center px-2 py-1 rounded @if(app()->getLocale() == 'de') bg-gray-200 @endif">
                                <img src="{{ asset('img/flags/de.png') }}" alt="🇩🇪" class="h-4 w-5 mr-1" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;12&quot;><rect width=&quot;20&quot; height=&quot;4&quot; fill=&quot;black&quot;/><rect width=&quot;20&quot; height=&quot;4&quot; y=&quot;4&quot; fill=&quot;red&quot;/><rect width=&quot;20&quot; height=&quot;4&quot; y=&quot;8&quot; fill=&quot;gold&quot;/></svg>';" />
                                <span class="text-sm">DE</span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="pt-2 pb-3 space-y-1">
                        <x-responsive-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')">
                            {{ __('Home') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="#jak-to-dziala">
                            {{ __('How it works') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="#korzyści">
                            {{ __('Benefits') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="#inwestycje">
                            {{ __('Investments') }}
                        </x-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    @auth
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="px-4">
                                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <x-responsive-nav-link :href="route('dashboard')">
                                    {{ __('Dashboard') }}
                                </x-responsive-nav-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-responsive-nav-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-responsive-nav-link>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="space-y-1">
                                <x-responsive-nav-link :href="route('login')">
                                    {{ __('Login') }}
                                </x-responsive-nav-link>
                                <x-responsive-nav-link :href="route('register')">
                                    {{ __('Register') }}
                                </x-responsive-nav-link>
                            </div>
                        </div>
                    @endauth
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-100">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="text-center text-sm text-gray-500">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Wszelkie prawa zastrzeżone.
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html> 