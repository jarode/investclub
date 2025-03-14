#!/bin/bash

# Kolory dla ładniejszego wyświetlania
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}InvestClub - Skrypt konfiguracyjny${NC}"
echo "=================================="
echo ""

# Sprawdź czy wymagane narzędzia są zainstalowane
check_command() {
    if ! command -v $1 &> /dev/null; then
        echo -e "${RED}$1 nie jest zainstalowany. Proszę zainstalować $1 przed kontynuacją.${NC}"
        exit 1
    else
        echo -e "${GREEN}✓ $1 jest zainstalowany${NC}"
    fi
}

echo -e "${YELLOW}Sprawdzanie wymagań...${NC}"
check_command php
check_command composer
check_command npm

# Sprawdź wersję PHP
php_version=$(php -r "echo PHP_VERSION;")
echo -e "${YELLOW}Wersja PHP: $php_version${NC}"
if [[ $php_version < "8.2" ]]; then
    echo -e "${RED}Wymagana jest wersja PHP >= 8.2${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Instalacja zależności PHP...${NC}"
composer install

echo ""
echo -e "${YELLOW}Instalacja zależności npm...${NC}"
npm install

# Sprawdź czy plik .env istnieje, jeśli nie - stwórz go
if [ ! -f ".env" ]; then
    echo ""
    echo -e "${YELLOW}Tworzenie pliku .env...${NC}"
    cp .env.example .env
    
    # Wygeneruj klucz aplikacji
    echo ""
    echo -e "${YELLOW}Generowanie klucza aplikacji...${NC}"
    php artisan key:generate
else
    echo ""
    echo -e "${GREEN}Plik .env już istnieje${NC}"
fi

# Kompiluj assety
echo ""
echo -e "${YELLOW}Kompilowanie assetów...${NC}"
npm run dev &

# Uruchom serwer
echo ""
echo -e "${YELLOW}Uruchamianie serwera Laravel...${NC}"
php artisan serve

# Zatrzymaj proces npm po naciśnięciu Ctrl+C
trap "kill 0" EXIT 