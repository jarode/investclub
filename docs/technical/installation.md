# Instalacja i Konfiguracja

## Informacje o Projekcie
- Repozytorium: https://github.com/jarode/investclub.git
- Środowisko Cloud: investplatform-main-r6pnjz.laravel.cloud
- Stack: Laravel 12 + Jetstream

## Wymagania Systemowe

### Minimalne wymagania
- PHP >= 8.2
- Composer 2.x
- Node.js >= 18.x
- Git

### Wymagane rozszerzenia PHP
- Ctype
- cURL
- DOM
- Fileinfo
- Filter
- Hash
- Mbstring
- OpenSSL
- PCRE
- PDO
- Session
- Tokenizer
- XML

## Instalacja Projektu

### 1. Pobranie repozytorium
```bash
git clone https://github.com/jarode/investclub.git
cd investclub
```

### 2. Instalacja zależności
```bash
composer install
npm install
```

### 3. Konfiguracja środowiska
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfiguracja bazy danych
1. Utwórz bazę danych MySQL
2. Zaktualizuj plik `.env` danymi dostępowymi do bazy

### 5. Migracja bazy danych
```bash
php artisan migrate
```

### 6. Kompilacja assetów
```bash
npm run build
```

### 7. Konfiguracja Stripe i Veriff
1. Utwórz konto w Stripe i Veriff
2. Zaktualizuj plik `.env` kluczami API

## Uruchomienie Aplikacji

### Środowisko deweloperskie
```bash
php artisan serve
npm run dev
```

### Środowisko produkcyjne
1. Skonfiguruj serwer web (nginx/Apache)
2. Ustaw odpowiednie uprawnienia dla katalogów:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```
3. Skonfiguruj zadania cron dla Laravel Scheduler:
   ```bash
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```
4. Skonfiguruj kolejki Laravel:
   ```bash
   php artisan queue:work
   ```

## Konfiguracja Laravel Cloud

### 1. Połączenie z Laravel Cloud
```bash
# Zaloguj się do Laravel Cloud przez CLI
laravel login

# Połącz projekt z Laravel Cloud
laravel cloud link investplatform-main-r6pnjz

# Sprawdź status połączenia
laravel cloud status
```

### 2. Konfiguracja środowiska
1. Skopiuj zmienne środowiskowe z panelu Laravel Cloud
2. Zaktualizuj ustawienia bazy danych i Redis
3. Skonfiguruj storage S3

### 3. Deployment
```bash
# Deploy na produkcję
git push cloud main

# Lub użyj Laravel Cloud CLI
laravel cloud deploy investplatform-main-r6pnjz
```

## Rozwiązywanie problemów

### Diagnostyka
```bash
php artisan about
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Logi
- Aplikacji: `storage/logs/laravel.log`
- Serwera: `/var/log/nginx/error.log`
- Queue: `php artisan queue:monitor`

## Monitoring i Utrzymanie

### 1. Logi i Monitoring
- Dostęp do logów przez Laravel Cloud Dashboard
- Monitoring metryk przez Laravel Cloud
- Integracja z zewnętrznymi narzędziami (np. Sentry)

### 2. Skalowanie
Laravel Cloud automatycznie obsługuje:
- Load Balancing
- Auto-scaling
- Distributed Cache
- Database Scaling

### 3. Backup
- Automatyczne backupy bazy danych
- Backup plików przez Object Storage
- Możliwość przywrócenia z dowolnego punktu

### 4. Bezpieczeństwo
- Automatyczne aktualizacje SSL
- DDoS protection
- Web Application Firewall
- Automatyczne skanowanie bezpieczeństwa

## Rozwiązywanie Problemów

### Typowe problemy
1. Problem z deploymentem
   - Sprawdź logi buildu
   - Sprawdź konfigurację środowiska
   - Zweryfikuj zmienne środowiskowe

2. Problem z wydajnością
   - Sprawdź metryki w Laravel Cloud
   - Zweryfikuj cache configuration
   - Sprawdź obciążenie bazy danych

3. Problem z kolejkami
   - Sprawdź logi workerów
   - Zweryfikuj konfigurację Horizon
   - Sprawdź połączenie Redis 