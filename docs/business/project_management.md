# Zarządzanie projektami i inwestycjami w InvestClub

## Model biznesowy projektów

InvestClub działa jako platforma łącząca właścicieli projektów inwestycyjnych z potencjalnymi inwestorami. System działa w modelu **Marketplace + Private Club**:

- Platforma prezentuje projekty inwestycyjne, ale nie pośredniczy w transakcjach
- Inwestorzy kontaktują się bezpośrednio z właścicielem projektu
- Transakcje odbywają się poza platformą
- Dostęp do projektów mają tylko zweryfikowani członkowie z aktywną subskrypcją

## Cykl życia projektu

### 1. Utworzenie projektu

Właściciel projektu po rejestracji i weryfikacji KYC oraz opłaceniu subskrypcji może utworzyć projekt inwestycyjny. Przy tworzeniu projektu należy podać:

- Nazwę projektu
- Szczegółowy opis
- Kwotę docelową finansowania
- Minimalną kwotę inwestycji
- Datę rozpoczęcia i zakończenia
- Przewidywany zwrot z inwestycji (%)
- Poziom ryzyka
- Kategorię projektu
- Lokalizację

Nowo utworzony projekt otrzymuje status `draft` (szkic).

### 2. Weryfikacja i aktywacja projektu

1. Projekt w statusie `draft` jest weryfikowany przez administratorów platformy
2. Po pozytywnej weryfikacji status projektu zmienia się na `active`
3. Projekty aktywne są widoczne dla wszystkich zweryfikowanych inwestorów z aktywną subskrypcją

### 3. Faza inwestycyjna

1. W czasie trwania projektu (od `start_date` do `end_date`) inwestorzy mogą zgłaszać zainteresowanie
2. Właściciel projektu kontaktuje się z zainteresowanymi inwestorami
3. Rozmowy i negocjacje prowadzone są poza platformą
4. Status inwestycji aktualizowany jest w systemie

### 4. Zakończenie projektu

1. Po upływie `end_date` projekt jest automatycznie oznaczany jako zakończony (`completed`)
2. Właściciel projektu i inwestorzy mogą aktualizować statusy inwestycji
3. Projekty zakończone pozostają widoczne w systemie jako archiwalne

## Statusy projektów

1. **draft** - Szkic, projekt w fazie przygotowania
2. **active** - Projekt aktywny, widoczny dla inwestorów
3. **completed** - Projekt zakończony
4. **cancelled** - Projekt anulowany

## Proces inwestycyjny

Inwestowanie w projekty na platformie InvestClub przebiega według następującego schematu:

### 1. Wyrażenie zainteresowania (status: `interested`)

1. Inwestor przegląda aktywne projekty dostępne na platformie
2. Po znalezieniu interesującego projektu, wyraża zainteresowanie:
   - Określa kwotę, którą potencjalnie może zainwestować
   - Wybiera preferowaną metodę kontaktu (telefon, email, spotkanie)
   - Podaje swoje dane kontaktowe
   - Może dodać dodatkowe uwagi dla właściciela projektu

### 2. Prowadzenie rozmów (status: `in_talks`)

1. Właściciel projektu otrzymuje powiadomienie o zainteresowaniu
2. Kontaktuje się z inwestorem wybraną metodą komunikacji
3. Obie strony prowadzą negocjacje i rozmowy poza platformą
4. Po rozpoczęciu rozmów, właściciel projektu aktualizuje status inwestycji na `in_talks`

### 3. Podpisanie umowy (status: `contract_signed`)

1. Po pozytywnym zakończeniu negocjacji, strony podpisują umowę poza platformą
2. Transakcja finansowa odbywa się poza platformą
3. Właściciel projektu aktualizuje status inwestycji na `contract_signed`

### 4. Anulowanie inwestycji (status: `cancelled`)

1. Na dowolnym etapie procesu, zarówno inwestor jak i właściciel projektu mogą anulować inwestycję
2. Status inwestycji zmienia się na `cancelled`
3. System zachowuje historię anulowanej inwestycji

## Struktura danych

### Model Project

```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->decimal('target_amount', 12, 2);
    $table->decimal('min_investment', 12, 2);
    $table->string('status')->default('draft');
    $table->date('start_date');
    $table->date('end_date');
    $table->decimal('returns_projection', 5, 2);
    $table->string('risk_level');
    $table->string('category');
    $table->string('location');
    $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
    $table->timestamps();
    $table->softDeletes();
});
```

### Model Investment

```php
Schema::create('investments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('project_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    
    // Statusy inwestycji
    $table->enum('status', ['interested', 'in_talks', 'contract_signed', 'cancelled'])
          ->default('interested');
    
    // Pola kontaktowe
    $table->string('contact_preference')->nullable();
    $table->text('contact_details')->nullable();
    
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Uprawnienia i dostęp

1. **Przeglądanie projektów**:
   - Tylko zweryfikowani użytkownicy z aktywną subskrypcją mogą przeglądać projekty
   - Użytkownicy bez weryfikacji KYC lub bez aktywnej subskrypcji widzą tylko ogólne informacje o platformie

2. **Tworzenie projektów**:
   - Tylko zweryfikowani użytkownicy z rolą właściciela projektu i aktywną subskrypcją mogą tworzyć projekty

3. **Inwestowanie**:
   - Tylko zweryfikowani inwestorzy z aktywną subskrypcją mogą wyrażać zainteresowanie projektami
   - Wartość deklarowanej inwestycji musi być większa lub równa minimalnej kwocie inwestycji określonej w projekcie

4. **Zarządzanie inwestycjami**:
   - Właściciel projektu może aktualizować statusy inwestycji
   - Inwestor może anulować swoją inwestycję przed podpisaniem umowy

## Przykładowy scenariusz

1. Anna, właścicielka projektu deweloperskiego, rejestruje się na platformie, przechodzi weryfikację KYC i wybiera subskrypcję Premium Owner.
2. Anna tworzy projekt "Apartamenty nad morzem" z kwotą docelową 5 000 000 PLN i minimalną inwestycją 100 000 PLN.
3. Administrator weryfikuje projekt i zmienia jego status na `active`.
4. Piotr, zweryfikowany inwestor z subskrypcją Premium Investor, przegląda projekty na platformie.
5. Piotr wyraża zainteresowanie projektem Anny, deklarując inwestycję 500 000 PLN i preferując kontakt telefoniczny.
6. Anna otrzymuje powiadomienie o zainteresowaniu, kontaktuje się z Piotrem i aktualizuje status inwestycji na `in_talks`.
7. Po spotkaniu i negocjacjach, Anna i Piotr podpisują umowę inwestycyjną poza platformą.
8. Anna aktualizuje status inwestycji na `contract_signed`.
9. Platforma zachowuje informacje o udanej transakcji dla celów statystycznych.

## Monitoring i analityka

System dostarcza narzędzia analityczne dla:

1. **Właścicieli projektów**:
   - Statystyki dotyczące zainteresowania projektem
   - Konwersja od zainteresowania do podpisanych umów
   - Całkowita zebrana kwota

2. **Inwestorów**:
   - Historia inwestycji
   - Wartość inwestycji według kategorii
   - Przewidywane zwroty z inwestycji

3. **Administratorów platformy**:
   - Ogólne statystyki platformy
   - Monitoring aktywności użytkowników
   - Wskaźniki konwersji dla różnych typów projektów 