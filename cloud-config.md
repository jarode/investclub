# Konfiguracja wdrożenia na Laravel Cloud

## Ustawienia aplikacji

Podczas tworzenia aplikacji w Laravel Cloud, ustaw następujące parametry:

1. **Nazwa aplikacji**: InvestClub
2. **Repo GitHub**: [Link do repozytorium]
3. **Branch**: `development` (dla środowiska testowego) lub `main` (dla produkcji)
4. **Region**: `eu-central` (lub najbliższy geograficznie)
5. **PHP version**: 8.3
6. **Node version**: 20

## Komendy wdrożeniowe

W panelu Laravel Cloud, w ustawieniach aplikacji, skonfiguruj komendy wdrożeniowe:

### Komenda Build
```sh
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Komenda Deploy
```sh
php artisan migrate --force
```

## Zasoby

Zaleca się skonfigurowanie następujących zasobów w Laravel Cloud:

1. **Baza danych MySQL**: 
   - Plan: Najniższy dla środowiska testowego (Standard 1GB dla produkcji)
   - Nazwa bazy: investclub

2. **Redis**:
   - Plan: najniższy dla środowiska testowego
   - Wykorzystanie: Cache, Kolejki, Sesje

3. **S3 Bucket** (opcjonalnie):
   - Region: eu-central-1
   - Dostęp: Prywatny
   - Wykorzystanie: Przechowywanie plików użytkowników, dokumentów itp.

## Zmienne środowiskowe

Wprowadź wszystkie wymagane zmienne środowiskowe z pliku `.env.example`:

Przykładowe zmienne, które trzeba dostosować:
- `APP_ENV=production` (dla produkcji) lub `APP_ENV=development` (dla środowiska testowego)
- `APP_DEBUG=false` (dla produkcji) lub `APP_DEBUG=true` (dla środowiska testowego)
- `APP_URL=` (URL wygenerowany przez Laravel Cloud)
- Dane dostępowe do bazy danych (uzupełnione automatycznie przez Laravel Cloud)
- Dane dostępowe do Redis (uzupełnione automatycznie przez Laravel Cloud)
- Dane dostępowe do S3 (jeśli skonfigurowano)
- Klucze Stripe dla KYC i płatności

## Certyfikat SSL

Włącz automatyczny certyfikat SSL dla swojej domeny w panelu Laravel Cloud.

## Monitoring

W zakładce "Metrics" możesz monitorować:
- Wykorzystanie zasobów
- Ruch HTTP
- Błędy aplikacji
- Czas odpowiedzi

## Logi

Logi aplikacji są dostępne w panelu Laravel Cloud w zakładce "Logs".

## Skalowanie

W miarę wzrostu ruchu, można łatwo zwiększyć zasoby aplikacji (CPU, pamięć) w panelu Laravel Cloud. 