#!/bin/bash

echo "=== SCRIPT DE DÉPLOIEMENT BACKEND ALLO MOBILE ==="

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}🚀 Début du déploiement...${NC}"

# 1. Vérifier les prérequis
echo -e "${YELLOW}📋 Vérification des prérequis...${NC}"
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP n'est pas installé${NC}"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer n'est pas installé${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Prérequis OK${NC}"

# 2. Installation des dépendances
echo -e "${YELLOW}📦 Installation des dépendances...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Dépendances installées${NC}"
else
    echo -e "${RED}❌ Erreur lors de l'installation des dépendances${NC}"
    exit 1
fi

# 3. Configuration de l'environnement
echo -e "${YELLOW}⚙️ Configuration de l'environnement...${NC}"
if [ ! -f .env ]; then
    echo -e "${RED}❌ Fichier .env manquant${NC}"
    echo "Créez un fichier .env basé sur .env.example"
    exit 1
fi

# 4. Génération de la clé d'application
echo -e "${YELLOW}🔑 Génération de la clé d'application...${NC}"
php artisan key:generate --force

# 5. Migration de la base de données
echo -e "${YELLOW}🗄️ Migration de la base de données...${NC}"
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Base de données migrée${NC}"
else
    echo -e "${RED}❌ Erreur lors de la migration${NC}"
    exit 1
fi

# 6. Création du lien symbolique pour le stockage
echo -e "${YELLOW}🔗 Création du lien symbolique...${NC}"
php artisan storage:link

# 7. Optimisation pour la production
echo -e "${YELLOW}⚡ Optimisation pour la production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Permissions des fichiers
echo -e "${YELLOW}🔐 Configuration des permissions...${NC}"
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 9. Vérification finale
echo -e "${YELLOW}🔍 Vérification finale...${NC}"
php artisan about

echo -e "${GREEN}🎉 Déploiement terminé avec succès !${NC}"
echo -e "${YELLOW}📝 N'oubliez pas de :${NC}"
echo "   - Configurer votre serveur web (Apache/Nginx)"
echo "   - Configurer SSL/HTTPS"
echo "   - Mettre à jour l'URL dans l'application mobile"
echo "   - Tester la communication backend-mobile"




