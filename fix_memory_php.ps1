# Script PowerShell pour corriger l'erreur de mémoire PHP
Write-Host "========================================" -ForegroundColor Green
Write-Host "   CORRECTION ERREUR MEMOIRE PHP" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

Write-Host "Configuration de la limite de memoire PHP..." -ForegroundColor Yellow
Write-Host ""

# Arrêter le serveur existant s'il fonctionne
Write-Host "Arret du serveur existant..." -ForegroundColor Cyan
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue

# Attendre un peu
Start-Sleep -Seconds 2

# Démarrer le serveur avec une limite de mémoire augmentée
Write-Host "Demarrage du serveur avec limite de memoire 4GB..." -ForegroundColor Green
Write-Host ""

# Utiliser php.ini personnalisé ou variables d'environnement
$env:PHP_INI_SCAN_DIR = ""
php -d memory_limit=4G -d max_execution_time=300 artisan serve --host=127.0.0.1 --port=8000

Write-Host ""
Write-Host "Serveur demarre avec limite de memoire augmentee." -ForegroundColor Green
Write-Host "URL: http://127.0.0.1:8000/admin/products" -ForegroundColor Cyan
Write-Host ""
Write-Host "Appuyez sur Ctrl+C pour arreter le serveur" -ForegroundColor Yellow

