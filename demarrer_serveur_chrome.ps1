# Script pour démarrer le serveur Laravel pour Chrome/Navigateur (localhost)

Write-Host "🌐 DÉMARRAGE DU SERVEUR LARAVEL POUR CHROME" -ForegroundColor Cyan
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier si un serveur tourne déjà sur le port 8000
$existingConnection = Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue
if ($existingConnection) {
    Write-Host "⚠️  Un serveur tourne déjà sur le port 8000" -ForegroundColor Yellow
    Write-Host "   Arrêt du serveur existant..." -ForegroundColor Yellow
    
    # Tuer les processus PHP qui pourraient être le serveur Laravel
    Get-Process php -ErrorAction SilentlyContinue | Where-Object {
        $_.CommandLine -like "*artisan serve*" -or $_.MainWindowTitle -eq ""
    } | Stop-Process -Force -ErrorAction SilentlyContinue
    
    Start-Sleep -Seconds 2
}

# Changer de répertoire
Set-Location $PSScriptRoot

Write-Host "✅ Configuration pour Chrome/Navigateur" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Le serveur sera accessible sur:" -ForegroundColor Cyan
Write-Host "   - Localhost: http://127.0.0.1:8000" -ForegroundColor White
Write-Host "   - Localhost: http://localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "🌐 Ouvrez Chrome et allez sur:" -ForegroundColor Yellow
Write-Host "   http://localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "⚠️  NOTE: Cette configuration est pour Chrome/Navigateur uniquement" -ForegroundColor Yellow
Write-Host "   Pour l'application mobile, utilisez: demarrer_serveur_reseau.ps1" -ForegroundColor Yellow
Write-Host ""

# Démarrer le serveur sur localhost uniquement
Write-Host "🔄 Démarrage du serveur Laravel..." -ForegroundColor Yellow
Write-Host ""

php artisan serve --host=127.0.0.1 --port=8000




