# CI/CD dla InvestClub

## Integracja z Laravel Cloud

Projekt korzysta z wbudowanego mechanizmu "Push to Deploy" w Laravel Cloud, co oznacza, że:

1. Każde wysłanie zmian do gałęzi `development` automatycznie inicjuje wdrożenie na środowisko testowe
2. Każde wysłanie zmian do gałęzi `main` automatycznie inicjuje wdrożenie na środowisko produkcyjne (gdy będzie gotowe)

## Przepływ pracy

1. Praca w gałęzi funkcyjnej (np. `feature/nowa-funkcja`)
2. Po zakończeniu prac, utworzenie Pull Requesta do gałęzi `development`
3. Po zatwierdzeniu, zmiany są łączone z gałęzią `development`
4. Laravel Cloud automatycznie wykrywa zmiany i inicjuje proces wdrożenia
5. Po testach w środowisku testowym, zmiany mogą być promowane do produkcji

## Monitorowanie wdrożeń

Wszystkie wdrożenia można monitorować bezpośrednio w panelu Laravel Cloud:
- https://cloud.laravel.com/ -> Applications -> InvestClub -> Deployments

## Struktura środowisk

### Development (Testowe)
- Branch: `development`
- URL: TBD (ustawiane przez Laravel Cloud)
- Skonfigurowane automatyczne migracje
- Debugowanie włączone

### Production (docelowo)
- Branch: `main`
- URL: TBD
- Skonfigurowane automatyczne migracje
- Debugowanie wyłączone
- Optymalizacja konfiguracji i cache włączone 