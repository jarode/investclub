# Wdrożenie POC na Laravel Cloud

## 1. Konfiguracja Laravel Cloud

### Wymagane kroki
1. Założenie konta w Laravel Cloud - https://cloud.laravel.com/
2. Utworzenie nowej organizacji (jeśli jeszcze nie istnieje)
3. Utworzenie nowej aplikacji
4. Połączenie repozytorium GitHub z aplikacją

### Process integracji z GitHub
1. W panelu Laravel Cloud wybierz "Create new application"
2. Wybierz opcję "Connect with GitHub"
3. Wybierz odpowiednie repozytorium
4. Wybierz gałąź `development` jako źródło kodu dla środowiska testowego
5. Skonfiguruj podstawowe ustawienia (rozmiar instancji, region, etc.)

## 2. Konfiguracja środowiska

### Zmienne środowiskowe
Wszystkie zmienne środowiskowe można konfigurować bezpośrednio w panelu Laravel Cloud:
1. Przejdź do zakładki "Environment"
2. Dodaj/edytuj zmienne środowiskowe

### Przykładowa konfiguracja dla POC
```
APP_NAME=InvestClub
APP_ENV=development
APP_DEBUG=true

DB_CONNECTION=sqlite

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

SUPPORTED_LOCALES=en,pl
DEFAULT_LOCALE=en
```

### Zasoby chmurowe
Laravel Cloud automatycznie zarządza zasobami. Dla POC warto skonfigurować:
- Key-Value Store (Redis) - dla cache i sesji
- Bucket (S3) - dla przechowywania plików

## 3. Automatyczne wdrożenia z GitHub

### Push to Deploy
Laravel Cloud automatycznie wdraża zmiany po wysłaniu ich do gałęzi `development`.
Proces wygląda następująco:
1. Programista wysyła zmiany do gałęzi development
2. Laravel Cloud wykrywa zmiany i rozpoczyna proces wdrożenia
3. Kod jest pobierany i budowany
4. Wykonywane są automatyczne migracje i inne komendy konfiguracyjne
5. Nowa wersja jest wdrażana z zerowym przestojem

### Dostosowanie procesu budowania
W panelu Laravel Cloud > Environment > Build and Deploy Commands można dostosować komendy wykonywane podczas wdrożenia:

**Build Commands**:
```
composer install --prefer-dist -o
php artisan event:cache
```

**Deploy Commands**:
```
php artisan migrate --force
```

## 4. Przygotowanie do wdrożenia

### Wymagane czynności przed wdrożeniem
1. Upewnij się, że aplikacja Laravel działa lokalnie
2. Sprawdź, czy wszystkie migracje działają poprawnie
3. Zbuduj i skompiluj assety lokalnie, aby wykryć ewentualne błędy
4. Wypchnij zmiany do gałęzi `development` na GitHub

## 5. Monitoring i debugowanie

### Logi
Laravel Cloud zapewnia dostęp do logów aplikacji bezpośrednio z panelu:
1. Przejdź do zakładki "Logs"
2. Możesz przeglądać logi aplikacji w czasie rzeczywistym

### Metryki
W zakładce "Metrics" możesz monitorować:
- Użycie CPU
- Zużycie pamięci
- Ruch HTTP
- Błędy 4xx i 5xx

### Komendy
Z poziomu panelu Laravel Cloud możesz uruchamiać komendy Artisan:
1. Przejdź do zakładki "Commands"
2. Wpisz komendę (np. `php artisan migrate:status`)

## 6. Zalecane praktyki

1. **Automatyzacja testów**:
   ```bash
   php artisan test
   ```
   Możesz dodać testy jako część procesu wdrożenia w CI/CD

2. **Zabezpieczanie wdrożeń**:
   - Włącz automatyczne testowanie przed wdrożeniem
   - Używaj funkcji "Rollback" w przypadku problemów

3. **Optymalizacja kosztów**:
   - Użyj najmniejszego możliwego rozmiaru instancji dla POC
   - Monitoruj zużycie zasobów i dostosowuj w razie potrzeby

4. **Bezpieczeństwo**:
   - Przechowuj poufne dane jako zmienne środowiskowe
   - Nie zapisuj kluczy i haseł w kodzie źródłowym 