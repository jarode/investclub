#!/bin/bash

# Kolory dla ładniejszego wyświetlania
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Konfiguracja gałęzi Git dla InvestClub${NC}"
echo "====================================="
echo ""

# Sprawdź, czy jesteśmy w repozytorium git
if [ ! -d ".git" ]; then
  echo -e "${YELLOW}Inicjalizacja repozytorium Git...${NC}"
  git init
  git add .
  git commit -m "Początkowy commit InvestClub"
  echo -e "${GREEN}Utworzono repozytorium Git${NC}"
else
  echo -e "${GREEN}Repozytorium Git już istnieje${NC}"
fi

# Upewnij się, że jesteśmy na gałęzi main
current_branch=$(git branch --show-current)
if [ "$current_branch" != "main" ]; then
  echo -e "${YELLOW}Przełączanie z $current_branch na main...${NC}"
  git branch -M main
  echo -e "${GREEN}Przełączono na gałąź main${NC}"
else
  echo -e "${GREEN}Już jesteś na gałęzi main${NC}"
fi

# Sprawdź, czy gałąź development istnieje
if git show-ref --verify --quiet refs/heads/development; then
  echo -e "${GREEN}Gałąź development już istnieje${NC}"
else
  echo -e "${YELLOW}Tworzenie gałęzi development...${NC}"
  git checkout -b development
  echo -e "${GREEN}Utworzono gałąź development${NC}"
fi

# Wyświetl wszystkie gałęzie
echo ""
echo -e "${YELLOW}Dostępne gałęzie:${NC}"
git branch

echo ""
echo -e "${GREEN}Konfiguracja zakończona!${NC}"
echo ""
echo "Aby wypchnąć zmiany do zdalnego repozytorium, użyj:"
echo "git remote add origin <URL_REPO>"
echo "git push -u origin main"
echo "git push -u origin development" 