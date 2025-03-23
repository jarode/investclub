# InvestClub

Platforma inwestycyjna działająca w modelu Marketplace + Private Club.

## Technologie

- PHP 8.3 + Laravel 11
- Livewire 3 + Filament
- MySQL
- Redis
- Stripe (weryfikacja KYC i zarządzanie subskrypcjami)

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

## Model biznesowy

### Jak to działa?

1. Platforma działa jako marketplace (katalog projektów inwestycyjnych)
   - Prezentujemy projekty inwestycyjne, ale nie pośredniczymy w transakcjach
   - Inwestorzy kontaktują się bezpośrednio z właścicielem projektu
   - Brak przechowywania środków – transakcje odbywają się poza platformą

2. Dostęp do marketplace mają tylko zweryfikowani członkowie („Private Club")
   - Rejestracja na platformie jest otwarta dla wszystkich
   - Dostęp do projektów i funkcjonalności platformy mają tylko użytkownicy po weryfikacji KYC przez system Stripe i opłaceniu abonamentu
   - System zarządzania subskrypcjami i płatnościami abonamentowymi realizowany jest przez Stripe
   - Pobieramy abonament od inwestorów i właścicieli projektów za dostęp do platformy

### Proces na platformie

1. Właściciel projektu rejestruje się, przechodzi weryfikację i publikuje ofertę
2. Oferta widoczna jest tylko dla zweryfikowanych inwestorów
3. Inwestorzy kontaktują się bezpośrednio z właścicielem projektu
4. Po uzyskaniu porozumienia transakcja odbywa się poza platformą
5. Platforma pobiera opłatę abonamentową za dostęp do ofert

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
- [Model biznesowy: Marketplace + Private Club](docs/business/marketplace_private_club.md)
- [Dokumentacja techniczna](docs/technical/development.md)
- [Architektura systemu](docs/technical/architecture.md)
- [Przewodnik implementacyjny](docs/technical/implementation_guide.md)
- [Plan wdrożenia POC](docs/technical/poc_implementation_plan.md)
- [Akceleracja rozwoju](docs/technical/acceleration.md)
- [Wdrażanie](docs/technical/deployment.md)
- [Plan testowania manualnego](docs/testing/manual_test_plan.md)
- [System subskrypcji](docs/subscription-system.md)

## Licencja

Własność [NAZWA FIRMY]. Wszelkie prawa zastrzeżone.
