<div class="relative language-switcher">
    <button id="language-switcher-button" class="flex items-center gap-x-1 text-sm font-medium text-gray-500 hover:text-gray-700">
        @php
            // Pobierz aktualny język z sesji lub domyślny z konfiguracji
            $currentLocale = session()->get('locale', config('app.locale'));
            // Debuguj język
            logger("Language Switcher - currentLocale: " . $currentLocale);
            logger("Language Switcher - app locale: " . app()->getLocale());
        @endphp
        
        @if($currentLocale == 'pl')
            <img src="{{ asset('img/flags/pl.png') }}" alt="🇵🇱" class="h-4 w-6 mr-1" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;12&quot;><rect width=&quot;20&quot; height=&quot;6&quot; fill=&quot;white&quot;/><rect width=&quot;20&quot; height=&quot;6&quot; y=&quot;6&quot; fill=&quot;red&quot;/></svg>';" /> Polski
        @elseif($currentLocale == 'en')
            <img src="{{ asset('img/flags/gb.png') }}" alt="🇬🇧" class="h-4 w-6 mr-1" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;12&quot; viewBox=&quot;0 0 60 30&quot;><clipPath id=&quot;s&quot;><path d=&quot;M0,0 v30 h60 v-30 z&quot;/></clipPath><clipPath id=&quot;t&quot;><path d=&quot;M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z&quot;/></clipPath><g clip-path=&quot;url(#s)&quot;><path d=&quot;M0,0 v30 h60 v-30 z&quot; fill=&quot;#012169&quot;/><path d=&quot;M0,0 L60,30 M60,0 L0,30&quot; stroke=&quot;#fff&quot; stroke-width=&quot;6&quot;/><path d=&quot;M0,0 L60,30 M60,0 L0,30&quot; clip-path=&quot;url(#t)&quot; stroke=&quot;#C8102E&quot; stroke-width=&quot;4&quot;/><path d=&quot;M30,0 v30 M0,15 h60&quot; stroke=&quot;#fff&quot; stroke-width=&quot;10&quot;/><path d=&quot;M30,0 v30 M0,15 h60&quot; stroke=&quot;#C8102E&quot; stroke-width=&quot;6&quot;/></g></svg>';" /> English
        @elseif($currentLocale == 'de')
            <img src="{{ asset('img/flags/de.png') }}" alt="🇩🇪" class="h-4 w-6 mr-1" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;12&quot;><rect width=&quot;20&quot; height=&quot;4&quot; fill=&quot;black&quot;/><rect width=&quot;20&quot; height=&quot;4&quot; y=&quot;4&quot; fill=&quot;red&quot;/><rect width=&quot;20&quot; height=&quot;4&quot; y=&quot;8&quot; fill=&quot;gold&quot;/></svg>';" /> Deutsch
        @endif
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div id="language-dropdown" class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
        <a href="/language/pl" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 @if($currentLocale == 'pl') bg-gray-100 @endif">
            <img src="{{ asset('img/flags/pl.png') }}" alt="🇵🇱" class="h-4 w-6 mr-2" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;12&quot;><rect width=&quot;20&quot; height=&quot;6&quot; fill=&quot;white&quot;/><rect width=&quot;20&quot; height=&quot;6&quot; y=&quot;6&quot; fill=&quot;red&quot;/></svg>';" /> Polski
        </a>
        <a href="/language/en" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 @if($currentLocale == 'en') bg-gray-100 @endif">
            <img src="{{ asset('img/flags/gb.png') }}" alt="🇬🇧" class="h-4 w-6 mr-2" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;12&quot; viewBox=&quot;0 0 60 30&quot;><clipPath id=&quot;s&quot;><path d=&quot;M0,0 v30 h60 v-30 z&quot;/></clipPath><clipPath id=&quot;t&quot;><path d=&quot;M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z&quot;/></clipPath><g clip-path=&quot;url(#s)&quot;><path d=&quot;M0,0 v30 h60 v-30 z&quot; fill=&quot;#012169&quot;/><path d=&quot;M0,0 L60,30 M60,0 L0,30&quot; stroke=&quot;#fff&quot; stroke-width=&quot;6&quot;/><path d=&quot;M0,0 L60,30 M60,0 L0,30&quot; clip-path=&quot;url(#t)&quot; stroke=&quot;#C8102E&quot; stroke-width=&quot;4&quot;/><path d=&quot;M30,0 v30 M0,15 h60&quot; stroke=&quot;#fff&quot; stroke-width=&quot;10&quot;/><path d=&quot;M30,0 v30 M0,15 h60&quot; stroke=&quot;#C8102E&quot; stroke-width=&quot;6&quot;/></g></svg>';" /> English
        </a>
        <a href="/language/de" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 @if($currentLocale == 'de') bg-gray-100 @endif">
            <img src="{{ asset('img/flags/de.png') }}" alt="🇩🇪" class="h-4 w-6 mr-2" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;12&quot;><rect width=&quot;20&quot; height=&quot;4&quot; fill=&quot;black&quot;/><rect width=&quot;20&quot; height=&quot;4&quot; y=&quot;4&quot; fill=&quot;red&quot;/><rect width=&quot;20&quot; height=&quot;4&quot; y=&quot;8&quot; fill=&quot;gold&quot;/></svg>';" /> Deutsch
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('language-switcher-button');
        const dropdown = document.getElementById('language-dropdown');
        
        button.addEventListener('click', function(event) {
            event.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.language-switcher')) {
                dropdown.classList.add('hidden');
            }
        });
    });
</script> 