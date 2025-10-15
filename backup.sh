#!/bin/bash

echo "💾 SAUVEGARDE ALLO MOBILE"
echo "========================"

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/allo-mobile"
PROJECT_DIR="/var/www/allo-mobile"

# Créer le dossier de sauvegarde
mkdir -p $BACKUP_DIR

# Sauvegarde base de données
echo "🗄️ Sauvegarde base de données..."
mysqldump -u allo_user -p allo_mobile_prod > $BACKUP_DIR/database_$DATE.sql

# Sauvegarde fichiers
echo "📁 Sauvegarde fichiers..."
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C /var/www allo-mobile

# Nettoyage anciennes sauvegardes (plus de 7 jours)
echo "🧹 Nettoyage anciennes sauvegardes..."
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "✅ Sauvegarde terminée: $DATE"