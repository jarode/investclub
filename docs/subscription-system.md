# System Subskrypcji InvestClub

## 1. Konfiguracja Stripe Customer Portal

### 1.1 Podstawowe ustawienia
- Portal jest włączony w systemie
- Nagłówek: "Zarządzanie subskrypcją InvestClub"
- Skonfigurowane URL-e dla:
  - Polityki prywatności
  - Warunków użytkowania
  - Wsparcia

### 1.2 Dozwolone funkcje
- Zarządzanie subskrypcją
- Zarządzanie metodami płatności
- Zarządzanie fakturami
- Zmiana planów subskrypcji

## 2. Przepływ Subskrypcji

### 2.1 Proces aktywacji
1. Użytkownik wybiera plan:
   - I-Free (darmowy plan dla inwestorów)
   - I-Premium (plan premium dla inwestorów)
   - O-Premium (plan premium dla właścicieli projektów)
2. System tworzy sesję Stripe Checkout
3. Po udanej płatności:
   - Użytkownik otrzymuje dostęp do Customer Portal
   - Subskrypcja jest aktywowana
   - Status jest aktualizowany w bazie danych

### 2.2 Zarządzanie subskrypcją
- Użytkownik może zarządzać subskrypcją przez portal
- Dostępne opcje:
  - Zmiana planu
  - Aktualizacja danych płatności
  - Przeglądanie historii płatności
  - Pobieranie faktur

## 3. Obsługa Webhooków

### 3.1 Obsługiwane zdarzenia
- `checkout.session.completed` - aktywacja subskrypcji
- `customer.subscription.updated` - zmiana planu
- `customer.subscription.deleted` - anulowanie
- `invoice.payment_failed` - problemy z płatnością
- `customer.subscription.trial_will_end` - koniec okresu próbnego

### 3.2 Statusy subskrypcji
- `active` - aktywna subskrypcja
- `past_due` - zaległa płatność
- `cancelled` - anulowana
- `unpaid` - nieopłacona

## 4. Zarządzanie Dostępem

### 4.1 Wymagania
Użytkownik ma dostęp do portalu gdy:
- Jest zalogowany
- Ma przypisane `stripe_customer_id`
- Ma aktywną subskrypcję (`stripe_subscription_id`)

### 4.2 Przycisk zarządzania
- Widoczny w interfejsie użytkownika
- Przekierowuje do Stripe Customer Portal
- Dostępny tylko dla uprawnionych użytkowników

## 5. Obsługa Błędów

### 5.1 Logowanie
- System loguje wszystkie zdarzenia związane z subskrypcją
- Szczegółowe informacje o błędach
- Historia zmian statusu

### 5.2 Powiadomienia
- Użytkownik jest powiadamiany o:
  - Problemach z płatnością
  - Zakończeniu okresu próbnego
  - Zmianach w subskrypcji

### 5.3 Zachowanie dostępu
- Przy anulowaniu subskrypcji:
  - Dostęp jest zachowany do końca okresu rozliczeniowego
  - Użytkownik może wrócić do planu darmowego

## 6. Bezpieczeństwo

### 6.1 Weryfikacja
- Weryfikacja podpisu webhooków
- Bezpieczne przechowywanie kluczy API
- Walidacja danych wejściowych

### 6.2 Transakcje
- Wszystkie operacje są wykonywane w transakcjach
- Automatyczny rollback w przypadku błędów
- Synchronizacja danych między Stripe a bazą danych

## 7. Testy

### 7.1 Testy jednostkowe
- Testy kontrolera subskrypcji
- Testy obsługi webhooków
- Mockowanie odpowiedzi Stripe

### 7.2 Testy integracyjne
- Testy przepływu subskrypcji
- Testy obsługi błędów
- Testy synchronizacji danych

## 8. Konfiguracja Środowiska

### 8.1 Zmienne środowiskowe
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_FREE_INVESTOR_PRICE_ID=price_...
STRIPE_PREMIUM_INVESTOR_PRICE_ID=price_...
STRIPE_PREMIUM_OWNER_PRICE_ID=price_...
```

### 8.2 Konfiguracja portalu
```php
'customer_portal' => [
    'enabled' => true,
    'headline' => 'Zarządzanie subskrypcją InvestClub',
    'privacy_policy_url' => env('STRIPE_CUSTOMER_PORTAL_PRIVACY_POLICY_URL'),
    'terms_url' => env('STRIPE_CUSTOMER_PORTAL_TERMS_URL'),
    'support_url' => env('STRIPE_CUSTOMER_PORTAL_SUPPORT_URL'),
    'features' => [
        'subscription_update' => true,
        'payment_method_update' => true,
        'invoice_history' => true,
    ],
],
``` 