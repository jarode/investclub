# Testowanie mechanizmów autoryzacji w InvestClub

## Wprowadzenie

Ten dokument opisuje jak testować wdrożone mechanizmy autoryzacji w aplikacji InvestClub. System autoryzacji opiera się na dwóch głównych mechanizmach:

1. **Middleware** - do kontroli dostępu na poziomie tras (routes)
2. **Polityki (Policies)** - do kontroli dostępu na poziomie zasobów (resources)

## Przygotowane elementy do testowania

W aplikacji zostały przygotowane następujące elementy:

### 1. Middleware dla ról
- `RoleMiddleware` - sprawdza, czy użytkownik ma wymaganą rolę

### 2. Polityki
- `UserPolicy` - zasady dostępu do zarządzania użytkownikami
- `InvestmentPolicy` - zasady dostępu do inwestycji

### 3. Widoki testowe
- Panel administratora (`/admin/dashboard`)
- Panel managera (`/manager/dashboard`)
- Zarządzanie użytkownikami (`/users`)
- Szczegóły użytkownika (`/users/{id}`)
- Edycja użytkownika (`/users/{id}/edit`)
- Dodawanie użytkownika (`/users/create`)
- Zarządzanie inwestycjami użytkownika (`/users/{id}/investments`)

### 4. Użytkownicy testowi
Seeder `UsersWithRolesSeeder` tworzy następujących użytkowników testowych:

- **Administrator** (admin@investclub.pl / password123)
- **Manager** (manager@investclub.pl / password123)
- **Inwestor** (investor@investclub.pl / password123)
- **Księgowy** (accountant@investclub.pl / password123)
- **Użytkownik** (user@investclub.pl / password123)
- **Nowy Inwestor** (new.investor@investclub.pl / password123)

## Scenariusze testowe

### Test 1: Middleware dla ról

#### Jako Administrator:
1. Zaloguj się jako administrator (admin@investclub.pl / password123)
2. Przejdź do `/admin/dashboard` - powinieneś mieć dostęp
3. Przejdź do `/manager/dashboard` - powinieneś otrzymać błąd 403 (brak uprawnień)

#### Jako Manager:
1. Zaloguj się jako manager (manager@investclub.pl / password123)
2. Przejdź do `/manager/dashboard` - powinieneś mieć dostęp
3. Przejdź do `/admin/dashboard` - powinieneś otrzymać błąd 403 (brak uprawnień)

#### Jako Zwykły użytkownik:
1. Zaloguj się jako zwykły użytkownik (user@investclub.pl / password123)
2. Przejdź do `/admin/dashboard` - powinieneś otrzymać błąd 403 (brak uprawnień)
3. Przejdź do `/manager/dashboard` - powinieneś otrzymać błąd 403 (brak uprawnień)

### Test 2: Polityka UserPolicy

#### Jako Administrator:
1. Zaloguj się jako administrator (admin@investclub.pl / password123)
2. Przejdź do `/users` - powinieneś widzieć listę wszystkich użytkowników
3. Dla każdego użytkownika powinieneś widzieć przyciski "Podgląd", "Edytuj", "Usuń" i "Inwestycje"
4. Powinieneś móc edytować każdego użytkownika
5. Powinieneś móc usunąć każdego użytkownika (poza sobą)

#### Jako Manager:
1. Zaloguj się jako manager (manager@investclub.pl / password123)
2. Przejdź do `/users` - powinieneś widzieć listę wszystkich użytkowników
3. Powinieneś widzieć przyciski "Podgląd", "Edytuj" i "Inwestycje"
4. Powinieneś móc edytować zwykłych użytkowników i inwestorów
5. Nie powinieneś móc edytować administratorów
6. Nie powinieneś widzieć przycisku "Usuń" dla żadnego użytkownika

#### Jako Zwykły użytkownik:
1. Zaloguj się jako zwykły użytkownik (user@investclub.pl / password123)
2. Próba dostępu do `/users` powinna być niedostępna
3. Możesz jednak zobaczyć swój własny profil (`/users/{twoje-id}`)
4. Możesz edytować tylko swój własny profil

### Test 3: Sprawdzanie viewFinancialData

#### Jako Administrator, Manager lub Księgowy:
1. Zaloguj się jako administrator, manager lub księgowy
2. Przejdź do profilu dowolnego użytkownika
3. Powinieneś widzieć sekcję "Stan portfela" z kwotą

#### Jako Inwestor lub Zwykły użytkownik:
1. Zaloguj się jako inwestor lub zwykły użytkownik
2. Przejdź do swojego profilu
3. Nie powinieneś widzieć sekcji "Stan portfela"

### Test 4: Zarządzanie inwestycjami (Gate::allows)

#### Jako Administrator, Manager lub Inwestor:
1. Zaloguj się jako administrator, manager lub inwestor
2. Przejdź do `/users/{id}/investments` dla dowolnego użytkownika
3. Powinieneś mieć dostęp do tej strony

#### Jako Zwykły użytkownik lub Księgowy:
1. Zaloguj się jako zwykły użytkownik lub księgowy
2. Spróbuj przejść do `/users/{id}/investments`
3. Powinieneś otrzymać błąd 403 (brak uprawnień)

## Jak uruchomić testy

1. Upewnij się, że baza danych jest gotowa:
   ```bash
   php artisan migrate:fresh
   ```

2. Uruchom seeder z testowymi użytkownikami:
   ```bash
   php artisan db:seed --class=UsersWithRolesSeeder
   ```

3. Uruchom serwer deweloperski:
   ```bash
   php artisan serve
   ```

4. Wykonaj powyższe scenariusze testowe logując się jako różni użytkownicy.

## Rozwiązywanie problemów

Jeśli testy nie przechodzą zgodnie z oczekiwaniami, sprawdź:

1. Czy middleware `RoleMiddleware` jest poprawnie zarejestrowane w `bootstrap/app.php`
2. Czy polityki są poprawnie zdefiniowane w katalogu `app/Policies`
3. Czy metody w modelu `User` (`hasRole`, `hasAnyRole`, `isAdmin`, `isVerified`) działają poprawnie
4. Czy trasy są poprawnie zdefiniowane w pliku `routes/web.php`
5. Czy widoki zawierają odpowiednie dyrektywy `@can` i `@cannot` 