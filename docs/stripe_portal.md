# Zarządzanie subskrypcją poprzez Stripe Customer Portal

## Wprowadzenie

InvestClub wykorzystuje Stripe Customer Portal do zarządzania subskrypcjami użytkowników. To rozwiązanie zapewnia bezpieczny i wygodny sposób zarządzania płatnościami, zmianami planów subskrypcji oraz dostępem do historii płatności.

## Dostęp do Portalu Stripe

Użytkownicy mają dostęp do Stripe Customer Portal bezpośrednio z dashboardu aplikacji za pomocą przycisku "Portal płatności Stripe". Po kliknięciu, użytkownik jest przekierowywany do bezpiecznego portalu hostowanego przez Stripe.

## Funkcje dostępne w Portalu Stripe

### 1. Zarządzanie subskrypcją
- Zmiana planu subskrypcji (upgrade/downgrade)
- Anulowanie subskrypcji
- Wznowienie anulowanej subskrypcji

### 2. Metody płatności
- Dodawanie nowych metod płatności
- Aktualizacja istniejących metod płatności
- Zmiana domyślnej metody płatności

### 3. Historia płatności
- Przeglądanie historii wszystkich płatności
- Pobieranie faktur
- Ponowne wysyłanie faktur na email

### 4. Dane do rozliczeń
- Aktualizacja adresu rozliczeniowego
- Zmiana danych firmy dla faktur

## Proces zmiany planu

1. Użytkownik loguje się do aplikacji InvestClub
2. Przechodzi do dashboardu i klika przycisk "Portal płatności Stripe"
3. W portalu Stripe wybiera opcję "Zmień plan"
4. Wybiera jeden z dostępnych planów:
   - I-Free (darmowy plan dla inwestorów)
   - I-Premium (plan premium dla inwestorów)
   - O-Premium (plan premium dla właścicieli projektów)
5. Potwierdza zmianę
6. Po potwierdzeniu, zmiany są automatycznie aktualizowane w aplikacji InvestClub

## Proces anulowania subskrypcji

1. Użytkownik loguje się do aplikacji InvestClub
2. Przechodzi do dashboardu i klika przycisk "Portal płatności Stripe"
3. W portalu Stripe wybiera opcję "Anuluj subskrypcję"
4. Potwierdza decyzję o anulowaniu
5. Użytkownik zachowuje dostęp do funkcji premium do końca bieżącego okresu rozliczeniowego
6. Po zakończeniu opłaconego okresu, użytkownik jest automatycznie przenoszony na plan darmowy (I-Free)

## Bezpieczeństwo

- Wszystkie operacje płatnicze są realizowane na infrastrukturze Stripe
- Żadne dane karty płatniczej nie są przechowywane na serwerach InvestClub
- Komunikacja z portalem Stripe odbywa się przez bezpieczne połączenie szyfrowane (HTTPS)
- Zmiany statusu subskrypcji są przekazywane do InvestClub poprzez zabezpieczone webhooki

## Najczęstsze problemy

1. **Brak dostępu do portalu**
   - Sprawdź, czy masz aktywną subskrypcję
   - Upewnij się, że jesteś zalogowany

2. **Problemy z płatnością**
   - Sprawdź, czy karta nie wygasła
   - Upewnij się, że masz wystarczające środki
   - Sprawdź, czy bank nie blokuje transakcji

3. **Brak aktualizacji po zmianie planu**
   - Odśwież stronę dashboardu
   - Wyloguj się i zaloguj ponownie
   - Jeśli problem się utrzymuje, skontaktuj się z obsługą klienta

## Wsparcie

W przypadku problemów z zarządzaniem subskrypcją lub pytań dotyczących płatności, prosimy o kontakt z naszym zespołem wsparcia poprzez:

- Email: support@investclub.pl
- Formularz kontaktowy w aplikacji
- Telefon wsparcia: +48 123 456 789 (w godzinach 9:00 - 17:00, pon-pt) 