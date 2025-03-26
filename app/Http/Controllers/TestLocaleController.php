<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class TestLocaleController extends Controller
{
    public function index()
    {
        // Szczegółowe logowanie
        Log::info('====== TEST LOCALE INDEX - START ======');
        Log::info('Current App Locale: ' . App::getLocale());
        Log::info('Current Session Locale: ' . Session::get('locale', 'brak'));
        Log::info('Session ID: ' . Session::getId());
        Log::info('Session zawartość: ', Session::all());
        Log::info('Konfiguracja app.locale: ' . config('app.locale'));
        Log::info('Konfiguracja app.available_locales: ', config('app.available_locales', []));
        Log::info('Middleware SetLocale załadowany: ' . (class_exists(\App\Http\Middleware\SetLocale::class) ? 'TAK' : 'NIE'));
        Log::info('Request Path: ' . request()->path());
        Log::info('Headers: ', request()->headers->all());
        Log::info('Cookies: ', request()->cookies->all());
        Log::info('====== TEST LOCALE INDEX - KONIEC ======');
        
        $debug = [
            'app_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
            'config_locale' => config('app.locale'),
            'config_available_locales' => config('app.available_locales'),
            'request_path' => request()->path(),
            'request_session_all' => Session::all(),
            'session_id' => Session::getId(),
            'is_new_session' => Session::isStarted() ? 'NIE' : 'TAK', 
            'cookies' => request()->cookies->all(),
            'headers' => array_map(function($item) {
                return is_array($item) ? implode(', ', $item) : $item;
            }, request()->headers->all()),
        ];
        
        return view('test.locale', compact('debug'));
    }
    
    public function setLocale($locale)
    {
        // Szczegółowe logowanie przed zmianą
        Log::info('====== TEST LOCALE SET - START ======');
        Log::info('Żądana zmiana języka na: ' . $locale);
        Log::info('Current App Locale (przed): ' . App::getLocale());
        Log::info('Current Session Locale (przed): ' . Session::get('locale', 'brak'));
        Log::info('Session ID (przed): ' . Session::getId());
        Log::info('Session zawartość (przed): ', Session::all());
        
        // Zapisujemy wartości przed zmianą
        $debug_before = [
            'app_locale_before' => App::getLocale(),
            'session_locale_before' => Session::get('locale'),
            'config_locale_before' => config('app.locale'),
        ];
        
        // Zmieniamy język
        if (in_array($locale, config('app.available_locales', ['en', 'pl', 'de']))) {
            // Wyczyść poprzedni język
            Session::forget('locale');
            Log::info('Wyczyszczono poprzedni język z sesji');
            
            // Ustaw nowy język
            Session::put('locale', $locale);
            App::setLocale($locale);
            config(['app.locale' => $locale]);
            
            // Wymuszamy zapis sesji
            Session::save();
            Log::info('Zapisano sesję po zmianie języka');
        } else {
            Log::warning('Próba ustawienia nieobsługiwanego języka: ' . $locale);
        }
        
        // Szczegółowe logowanie po zmianie
        Log::info('Current App Locale (po): ' . App::getLocale());
        Log::info('Current Session Locale (po): ' . Session::get('locale', 'brak'));
        Log::info('Session ID (po): ' . Session::getId());
        Log::info('Session zawartość (po): ', Session::all());
        Log::info('Middleware SetLocale loaded: ' . (class_exists(\App\Http\Middleware\SetLocale::class) ? 'TAK' : 'NIE'));
        Log::info('====== TEST LOCALE SET - KONIEC ======');
        
        // Zapisujemy wartości po zmianie
        $debug_after = [
            'app_locale_after' => App::getLocale(),
            'session_locale_after' => Session::get('locale'),
            'config_locale_after' => config('app.locale'),
        ];
        
        // Łączymy obie tablice
        $debug = array_merge($debug_before, $debug_after, [
            'request_path' => request()->path(),
            'all_cookies' => request()->cookies->all(),
            'session_id' => Session::getId(),
            'is_session_saved' => Session::isStarted() ? 'TAK' : 'NIE',
            'all_headers' => array_map(function($item) {
                return is_array($item) ? implode(', ', $item) : $item;
            }, request()->headers->all()),
        ]);
        
        return view('test.locale_changed', compact('debug', 'locale'));
    }
}
