# InvestClub

Platforma inwestycyjna działająca w modelu Marketplace + Private Club.

## Technologie

- PHP 8.3 + Laravel 11
- Livewire 3 + Filament
- MySQL
- Redis
- Stripe (KYC i płatności)

## Wymagania

- PHP 8.3+
- Composer 2+
- Node.js 20+
- MySQL 8+
- Redis (opcjonalnie, ale zalecane)

## Instalacja lokalna

1. Sklonuj repozytorium:

```bash
git clone [url-repozytorium] investclub
cd investclub
```

2. Zainstaluj zależności PHP:

```bash
composer install
```

3. Zainstaluj zależności JavaScript:

```bash
npm install
```

4. Skopiuj plik `.env.example` do `.env` i dostosuj konfigurację:

```bash
cp .env.example .env
```

5. Wygeneruj klucz aplikacji:

```bash
php artisan key:generate
```

6. Uruchom migracje:

```bash
php artisan migrate
```

7. Opcjonalnie, wczytaj dane testowe:

```bash
php artisan db:seed
```

8. Skompiluj zasoby frontend:

```bash
npm run dev
```

9. Uruchom serwer:

```bash
php artisan serve
```

Aplikacja będzie dostępna pod adresem: http://localhost:8000

## Struktura projektu

- `app/` - Główny kod aplikacji
  - `Models/` - Modele danych
  - `Http/Controllers/` - Kontrolery aplikacji
  - `Http/Livewire/` - Komponenty Livewire
  - `Services/` - Serwisy aplikacji
  - `Filament/` - Zasoby panelu administracyjnego
- `database/migrations/` - Migracje bazy danych
- `resources/views/` - Widoki aplikacji
- `routes/` - Definicje tras
- `docs/` - Dokumentacja projektu

## Wdrażanie

Projekt jest skonfigurowany do automatycznego wdrażania na Laravel Cloud. Szczegóły:

- [Dokumentacja CI/CD](.github/workflows/README.md)
- [Szczegółowa konfiguracja Laravel Cloud](cloud-config.md)

### Gałęzie i środowiska

- `development` - Środowisko testowe
- `main` - Środowisko produkcyjne (przyszłe)

## Dokumentacja

- [Procesy biznesowe](docs/business/processes.md)
- [Dokumentacja techniczna](docs/technical/development.md)
- [Integracja ze Stripe](docs/technical/stripe_integration.md)
- [Akceleracja rozwoju](docs/technical/acceleration.md)
- [Wdrażanie](docs/technical/deployment.md)

## Licencja

Własność [NAZWA FIRMY]. Wszelkie prawa zastrzeżone.
