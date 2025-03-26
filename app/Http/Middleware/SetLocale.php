<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Obsługiwane języki
     */
    protected $supportedLocales;
    
    /**
     * Domyślny język aplikacji
     */
    protected $defaultLocale = 'en';

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->supportedLocales = config('app.available_locales', ['en', 'pl', 'de']);
        $this->defaultLocale = config('app.locale', 'en');
        
        // Logujemy inicjalizację middleware
        Log::info('====== MIDDLEWARE SETLOCALE CONSTRUCT ======');
        Log::info('  Obsługiwane lokalizacje: ' . implode(', ', $this->supportedLocales));
        Log::info('  Domyślny język: ' . $this->defaultLocale);
        Log::info('  Session ID: ' . Session::getId());
        Log::info('  Session started: ' . (Session::isStarted() ? 'TAK' : 'NIE'));
        Log::info('====== MIDDLEWARE SETLOCALE CONSTRUCT END ======');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('====== MIDDLEWARE SETLOCALE HANDLE START ======');
        Log::info('URL żądania: ' . $request->fullUrl());
        Log::info('Metoda: ' . $request->method());
        Log::info('Path: ' . $request->path());
        Log::info('Session ID przed: ' . Session::getId());
        Log::info('Session started przed: ' . (Session::isStarted() ? 'TAK' : 'NIE'));
        Log::info('Bieżący język App::getLocale() przed: ' . App::getLocale());
        Log::info('Bieżący język w sesji przed: ' . Session::get('locale', 'brak'));
        
        // Pobierz język z sesji lub domyślny
        $locale = Session::get('locale', $this->defaultLocale);
        Log::info('Pobrany język z sesji: ' . $locale);
        
        // Upewnij się, że locale jest obsługiwane
        if (!in_array($locale, $this->supportedLocales)) {
            Log::warning('Język z sesji nie jest obsługiwany, używam domyślnego');
            $locale = $this->defaultLocale;
        }
        
        // Ustaw locale
        App::setLocale($locale);
        
        // Zapisz w sesji
        Session::put('locale', $locale);
        
        // Dodaj do widoku
        view()->share('currentLocale', $locale);
        
        // Dodatkowe logowanie po ustawieniu języka
        Log::info('Ustawiony język App::getLocale(): ' . App::getLocale());
        Log::info('Ustawiony język w sesji: ' . Session::get('locale', 'brak'));
        Log::info('Session ID po: ' . Session::getId());
        Log::info('Session started po: ' . (Session::isStarted() ? 'TAK' : 'NIE'));
        Log::info('====== MIDDLEWARE SETLOCALE HANDLE END ======');
        
        $response = $next($request);
        
        // Logowanie po przetworzeniu
        Log::info('====== MIDDLEWARE SETLOCALE AFTER RESPONSE ======');
        Log::info('Języka App::getLocale() po response: ' . App::getLocale());
        Log::info('Język w sesji po response: ' . Session::get('locale', 'brak'));
        Log::info('Session ID po response: ' . Session::getId());
        Log::info('Typ odpowiedzi: ' . get_class($response));
        Log::info('Status odpowiedzi: ' . $response->getStatusCode());
        Log::info('====== MIDDLEWARE SETLOCALE AFTER RESPONSE END ======');
        
        return $response;
    }
} 