# Plan testowania manualnego dla InvestClub

## Dane testowe

### Użytkownicy
| Login | Hasło | Rola | Status KYC |
|-------|-------|------|------------|
| admin@investclub.pl | Test1234! | admin | zatwierdzony |
| manager@investclub.pl | Test1234! | manager | zatwierdzony |
| inwestor@investclub.pl | Test1234! | investor | zatwierdzony |
| inwestor2@investclub.pl | Test1234! | investor | w trakcie |

### Projekty
| Nazwa | Wartość projektu | Min. zaangażowanie | Status | Właściciel |
|-------|------------------|-------------------|--------|------------|
| Budowa biurowca Alpha | 10 000 000 zł | 1 000 000 zł | aktywny | manager |
| Apartamenty Omega | 20 000 000 zł | 2 000 000 zł | aktywny | manager |
| TechStartup Beta | 5 000 000 zł | 500 000 zł | szkic (draft) | manager |

## Plan testowania modułu inwestycji

### 1. Przeglądanie dostępnych projektów

**Rola: Inwestor z weryfikacją KYC**

1. Zaloguj się jako inwestor@investclub.pl (zweryfikowany inwestor)
2. Przejdź do katalogu projektów (Projekty → Katalog)
3. Sprawdź, czy widoczne są aktywne projekty
4. Zastosuj filtr statusu "aktywny"
   - *Oczekiwany rezultat*: Widoczne powinny być tylko projekty "Budowa biurowca Alpha" i "Apartamenty Omega"
5. Zastosuj filtr statusu "szkic"
   - *Oczekiwany rezultat*: Nie powinny być widoczne projekty w statusie "szkic" dla zwykłego inwestora
6. Zresetuj filtry i kliknij w szczegóły projektu "Budowa biurowca Alpha"
   - *Oczekiwany rezultat*: Strona z detalami projektu powinna zawierać wszystkie informacje o projekcie oraz przycisk "Wyraź zainteresowanie"

### 2. Ograniczenia dla niezweryfikowanych użytkowników

**Rola: Inwestor bez weryfikacji KYC**

1. Zaloguj się jako inwestor2@investclub.pl (niezweryfikowany inwestor)
2. Spróbuj przejść do katalogu projektów
   - *Oczekiwany rezultat*: System informuje o konieczności ukończenia weryfikacji KYC
3. Przejdź do swojego profilu
4. Sprawdź sekcję KYC i rozpocznij proces weryfikacji
   - *Oczekiwany rezultat*: Formularz do wprowadzenia danych KYC jest dostępny

### 3. Wyrażenie zainteresowania projektem

**Rola: Inwestor z weryfikacją KYC**

1. Zaloguj się jako inwestor@investclub.pl
2. Przejdź do szczegółów projektu "Budowa biurowca Alpha"
3. Kliknij przycisk "Wyraź zainteresowanie"
4. Wypełnij formularz:
   - Deklarowana kwota: 1 500 000 zł
   - Preferowana metoda kontaktu: Email
   - Dane kontaktowe: inwestor@example.com
   - Uwagi: "Zainteresowany udziałem w projekcie"
5. Kliknij "Wyślij deklarację"
   - *Oczekiwany rezultat*: Komunikat o pomyślnym wysłaniu deklaracji oraz przekierowanie do szczegółów
6. Przejdź do zakładki "Moje zainteresowania"
   - *Oczekiwany rezultat*: Na liście powinna pojawić się nowa deklaracja ze statusem "Zainteresowany"

### 4. Próba deklaracji poniżej minimalnego zaangażowania

**Rola: Inwestor z weryfikacją KYC**

1. Zaloguj się jako inwestor@investclub.pl
2. Przejdź do szczegółów projektu "Apartamenty Omega"
3. Kliknij przycisk "Wyraź zainteresowanie"
4. Wypełnij formularz:
   - Deklarowana kwota: 1 000 000 zł (poniżej minimalnego zaangażowania 2 000 000 zł)
   - Preferowana metoda kontaktu: Email
   - Dane kontaktowe: inwestor@example.com
5. Kliknij "Wyślij deklarację"
   - *Oczekiwany rezultat*: Komunikat o błędzie informujący o zbyt niskiej kwocie zaangażowania

### 5. Aktualizacja deklaracji zainteresowania

**Rola: Inwestor z weryfikacją KYC**

1. Zaloguj się jako inwestor@investclub.pl
2. Przejdź do "Moje zainteresowania"
3. Znajdź deklarację utworzoną w teście #3
4. Kliknij przycisk "Edytuj"
5. Zmień deklarowaną kwotę na 2 000 000 zł
6. Kliknij "Aktualizuj deklarację"
   - *Oczekiwany rezultat*: Komunikat o pomyślnej aktualizacji oraz zaktualizowana kwota widoczna w szczegółach

### 6. System komunikacji bezpośredniej

**Role: Inwestor i Manager**

1. Zaloguj się jako manager@investclub.pl
2. Przejdź do szczegółów projektu z deklaracją z testu #5
3. Otwórz zakładkę "Zainteresowani inwestorzy"
4. Znajdź deklarację inwestora
5. Kliknij "Rozpocznij rozmowy"
   - *Oczekiwany rezultat*: Status deklaracji zmienia się na "W trakcie rozmów"
6. Sprawdź dane kontaktowe inwestora
   - *Oczekiwany rezultat*: Widoczne są preferencje kontaktu i dane kontaktowe
7. Wyloguj się i zaloguj jako inwestor@investclub.pl
8. Sprawdź status swojej deklaracji
   - *Oczekiwany rezultat*: Status powinien być "W trakcie rozmów"

### 7. Zaznaczenie podpisania umowy

**Rola: Manager**

1. Zaloguj się jako manager@investclub.pl
2. Przejdź do "Zarządzanie projektami"
3. Wybierz projekt "Budowa biurowca Alpha"
4. Przejdź do zakładki "Zainteresowani inwestorzy"
5. Wybierz deklarację w statusie "W trakcie rozmów"
6. Kliknij "Oznacz jako umowa podpisana"
   - *Oczekiwany rezultat*: Status zmienia się na "Umowa podpisana"
7. Wyloguj się i zaloguj jako inwestor@investclub.pl
8. Sprawdź status swojej deklaracji
   - *Oczekiwany rezultat*: Status powinien być "Umowa podpisana"

### 8. Anulowanie zainteresowania

**Rola: Inwestor**

1. Zaloguj się jako inwestor@investclub.pl
2. Utwórz nową deklarację zainteresowania dla projektu "Apartamenty Omega"
3. Przejdź do "Moje zainteresowania"
4. Znajdź nową deklarację
5. Kliknij przycisk "Anuluj"
6. Potwierdź anulowanie
   - *Oczekiwany rezultat*: Status zmienia się na "Anulowane", manager projektu otrzymuje powiadomienie

### 9. Zarządzanie projektami przez managera

**Rola: Manager**

1. Zaloguj się jako manager@investclub.pl
2. Przejdź do "Zarządzanie projektami"
3. Utwórz nowy projekt:
   - Nazwa: "Projekt Testowy"
   - Opis: "Opis projektu testowego"
   - Kwota docelowa: 5 000 000 zł
   - Min. inwestycja: 500 000 zł
   - Kategoria: "nieruchomości mieszkaniowe"
   - Lokalizacja: "Kraków"
4. Zapisz projekt
   - *Oczekiwany rezultat*: Projekt zostaje utworzony ze statusem "szkic"
5. Edytuj projekt i wypełnij wszystkie pozostałe pola
6. Wyloguj się i zaloguj jako admin@investclub.pl
7. Przejdź do szczegółów projektu "Projekt Testowy"
8. Zmień status projektu na "aktywny"
   - *Oczekiwany rezultat*: Status projektu zmienia się na "aktywny", projekt jest widoczny dla wszystkich zweryfikowanych inwestorów

### 10. Monitoring przez administratora

**Rola: Administrator**

1. Zaloguj się jako admin@investclub.pl
2. Przejdź do panelu administracyjnego
3. Sprawdź sekcję "Aktywność platformy"
4. Przejrzyj statystyki:
   - Liczba aktywnych projektów
   - Liczba deklaracji zainteresowania
   - Liczba prowadzonych rozmów
   - Liczba sfinalizowanych umów
   - *Oczekiwany rezultat*: Wszystkie statystyki są aktualne i poprawne

### 11. Weryfikacja KYC

**Role: Inwestor 2, Administrator**

1. Zaloguj się jako inwestor2@investclub.pl
2. Przejdź do ustawień profilu
3. Uzupełnij dane KYC i wyślij do weryfikacji
4. Wyloguj się i zaloguj jako admin@investclub.pl
5. Przejdź do panelu weryfikacji KYC
6. Znajdź zgłoszenie inwestora2
7. Zatwierdź weryfikację
   - *Oczekiwany rezultat*: Status KYC zmienia się na "zatwierdzony", inwestor otrzymuje powiadomienie
8. Wyloguj się i zaloguj ponownie jako inwestor2@investclub.pl
9. Spróbuj przejść do katalogu projektów
   - *Oczekiwany rezultat*: Dostęp do katalogu projektów jest możliwy

### 12. System powiadomień

**Role: Inwestor, Manager**

1. Zaloguj się jako inwestor@investclub.pl
2. Sprawdź centrum powiadomień
3. Zweryfikuj otrzymywanie powiadomień o:
   - Zmianach statusu deklaracji
   - Aktualizacjach w projektach
   - Zatwierdzeniu weryfikacji KYC
4. Wyloguj się i zaloguj jako manager@investclub.pl
5. Sprawdź centrum powiadomień
6. Zweryfikuj otrzymywanie powiadomień o:
   - Nowych deklaracjach zainteresowania
   - Anulowanych deklaracjach
   - *Oczekiwany rezultat*: Wszystkie powiadomienia są dostarczane prawidłowo

## Lista funkcji do przetestowania (podsumowanie)

1. Rejestracja i weryfikacja użytkownika (KYC)
2. Przeglądanie katalogu projektów (tylko dla zweryfikowanych użytkowników)
3. Filtrowanie i wyszukiwanie projektów
4. Wyrażanie zainteresowania projektem
5. Zarządzanie statusami deklaracji (zainteresowany, w trakcie rozmów, umowa podpisana)
6. Bezpośrednia komunikacja między inwestorem a managerem (dane kontaktowe)
7. System powiadomień
8. Monitoring aktywności
9. Uprawnienia dostępu dla różnych ról
10. Bezpieczeństwo i prywatność danych 