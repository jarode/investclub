#!/bin/bash

# Kolory dla ładniejszego wyświetlania
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}InvestClub - Naprawianie uprawnień plików${NC}"
echo "=========================================="
echo ""

# Sprawdź czy skrypt jest uruchamiany jako root/sudo
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Ten skrypt wymaga uprawnień administratora (sudo).${NC}"
    echo -e "Uruchom jako: ${YELLOW}sudo $0${NC}"
    exit 1
fi

# Pobierz nazwę użytkownika i grupy
read -p "Podaj nazwę użytkownika serwera WWW (np. www-data): " WEB_USER
read -p "Podaj nazwę grupy serwera WWW (np. www-data): " WEB_GROUP

# Upewnij się, że użytkownik i grupa istnieją
if ! id "$WEB_USER" &>/dev/null; then
    echo -e "${RED}Użytkownik $WEB_USER nie istnieje.${NC}"
    exit 1
fi

if ! getent group "$WEB_GROUP" &>/dev/null; then
    echo -e "${RED}Grupa $WEB_GROUP nie istnieje.${NC}"
    exit 1
fi

echo -e "${YELLOW}Ustawianie właściciela plików...${NC}"
chown -R $WEB_USER:$WEB_GROUP .
echo -e "${GREEN}✓ Właściciel ustawiony na $WEB_USER:$WEB_GROUP${NC}"

echo -e "${YELLOW}Ustawianie uprawnień dla katalogów...${NC}"
find . -type d -exec chmod 755 {} \;
echo -e "${GREEN}✓ Uprawnienia katalogów ustawione na 755${NC}"

echo -e "${YELLOW}Ustawianie uprawnień dla plików...${NC}"
find . -type f -exec chmod 644 {} \;
echo -e "${GREEN}✓ Uprawnienia plików ustawione na 644${NC}"

echo -e "${YELLOW}Ustawianie uprawnień dla skryptów Bash...${NC}"
find . -name "*.sh" -exec chmod +x {} \;
echo -e "${GREEN}✓ Skrypty Bash są teraz wykonywalne${NC}"

echo -e "${YELLOW}Ustawianie specjalnych uprawnień dla katalogów storage i bootstrap/cache...${NC}"
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Katalogi storage i bootstrap/cache mają uprawnienia 775${NC}"

echo ""
echo -e "${GREEN}Wszystkie uprawnienia zostały poprawnie ustawione!${NC}" 