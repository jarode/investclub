# Model biznesowy: Marketplace + Private Club

## Jak to działa?

### 1. Platforma działa jako marketplace (katalog projektów inwestycyjnych)
- Prezentujemy projekty inwestycyjne, ale nie pośredniczymy w transakcjach
- Inwestorzy kontaktują się bezpośrednio z właścicielem projektu
- Brak przechowywania środków – transakcje odbywają się poza platformą

### 2. Dostęp do marketplace mają tylko zweryfikowani członkowie („Private Club")
- Rejestracja na platformie jest otwarta dla wszystkich
- Dostęp do projektów i funkcjonalności platformy mają tylko użytkownicy po weryfikacji KYC przez system Stripe i opłaceniu abonamentu
- System zarządzania subskrypcjami i płatnościami abonamentowymi realizowany jest przez Stripe
- Pobieramy abonament od inwestorów i właścicieli projektów za dostęp do platformy

## Proces na platformie

1. Właściciel projektu rejestruje się, przechodzi weryfikację i publikuje ofertę
2. Oferta widoczna jest tylko dla zweryfikowanych inwestorów
3. Inwestorzy kontaktują się bezpośrednio z właścicielem projektu
4. Po uzyskaniu porozumienia transakcja odbywa się poza platformą
5. Platforma pobiera opłatę abonamentową za dostęp do ofert

## Statusy inwestycji

### 1. Zainteresowany (interested)
- Inwestor wyraża wstępne zainteresowanie projektem
- Podaje preferowaną metodę kontaktu i dane kontaktowe
- Określa kwotę, którą potencjalnie może zainwestować

### 2. W trakcie rozmów (in_talks)
- Manager projektu kontaktuje się z inwestorem
- Obie strony prowadzą negocjacje i rozmowy poza platformą
- Platforma śledzi tylko status procesu

### 3. Umowa podpisana (contract_signed)
- Transakcja została sfinalizowana poza platformą
- Umowa została podpisana między stronami
- Platforma rejestruje zakończenie procesu

### 4. Anulowane (cancelled)
- Inwestor lub manager może anulować proces na dowolnym etapie
- Transakcja nie doszła do skutku

## Implementacja techniczna

Główne zmiany wprowadzone w systemie:

1. **Usunięto mechanizmy bezpośrednich płatności za inwestycje**
   - Usunięto pole `wallet_balance` z tabeli użytkowników
   - Usunięto pole `current_amount` z tabeli projektów
   - Usunięto pole `transaction_reference` z tabeli inwestycji

2. **Dodano system weryfikacji KYC i subskrypcji poprzez Stripe**
   - Pole `kyc_status` w tabeli użytkowników do śledzenia procesu weryfikacji
   - Integracja ze Stripe dla procesu weryfikacji KYC
   - System subskrypcji i płatności abonamentowych zarządzany przez Stripe
   - Wymaganie weryfikacji KYC i aktywnej subskrypcji przed uzyskaniem dostępu do projektów
   - Ograniczenie tworzenia inwestycji tylko dla zweryfikowanych użytkowników z aktywną subskrypcją

3. **Zmieniono statusy inwestycji**
   - Zastąpiono stare statusy (`declared`, `paid`, `confirmed`) nowymi statusami
   - Nowe statusy odzwierciedlają model biznesowy bez płatności online
   - Dodano pola `contact_preference` i `contact_details` dla bezpośredniej komunikacji

4. **Zaktualizowano zabezpieczenia i polityki**
   - Polityka projektów - tylko zweryfikowani użytkownicy mają dostęp
   - Polityka inwestycji - nowe reguły dla statusów inwestycji
   - Ograniczenia widoczności projektów zależne od statusu użytkownika

## Zgodność z regulacjami

Model Marketplace + Private Club pozwala na uniknięcie większości regulacji dotyczących platform crowdfundingowych:

1. **Brak kwalifikacji jako ECSP (European Crowdfunding Service Provider)**
   - Brak pośrednictwa w transakcjach
   - Brak przechowywania środków klientów
   - Brak prowizji od transakcji

2. **Zgodność z AML/KYC**
   - Wszyscy użytkownicy są weryfikowani
   - Brak anonimowych transakcji
   - Pełna identyfikacja użytkowników

3. **Ochrona danych osobowych**
   - Zgodność z RODO
   - Bezpieczne przechowywanie danych kontaktowych
   - Transparentne przekazywanie danych między zainteresowanymi stronami 