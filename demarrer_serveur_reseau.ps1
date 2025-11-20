# Script pour démarrer le serveur Laravel accessible depuis le réseau

Write-Host "🚀 DÉMARRAGE DU SERVEUR LARAVEL POUR RÉSEAU" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier si un serveur tourne déjà sur le port 8000
$existingConnection = Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue
if ($existingConnection) {
    Write-Host "⚠️  Un serveur tourne déjà sur le port 8000" -ForegroundColor Yellow
    Write-Host "   Arrêt du serveur existant..." -ForegroundColor Yellow
    $process = Get-Process | Where-Object {$_.CommandLine -like "*artisan serve*"} | Select-Object -First 1
    if ($process) {
        Stop-Process -Id $process.Id -Force
        Start-Sleep -Seconds 2
    }
}

# Obtenir l'IP de l'ordinateur
$ipAddress = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object {$_.IPAddress -notlike "127.*" -and $_.IPAddress -notlike "169.*"} | Select-Object -First 1).IPAddress

if (-not $ipAddress) {
    Write-Host "❌ Impossible de détecter l'IP de l'ordinateur" -ForegroundColor Red
    Write-Host "   Utilisation de 0.0.0.0 (accessible depuis toutes les interfaces)" -ForegroundColor Yellow
    $ipAddress = "0.0.0.0"
} else {
    Write-Host "✅ IP détectée: $ipAddress" -ForegroundColor Green
    Write-Host ""
    Write-Host "📱 Mettez à jour l'IP dans l'application mobile:" -ForegroundColor Cyan
    Write-Host "   Fichier: gestion_commandes_mobile/lib/core/config/backend_config.dart" -ForegroundColor White
    Write-Host "   Changez baseHost en: http://$ipAddress:8000" -ForegroundColor White
    Write-Host ""
}

# Changer de répertoire
Set-Location $PSScriptRoot

Write-Host "🔄 Démarrage du serveur Laravel..." -ForegroundColor Yellow
Write-Host "   Le serveur sera accessible depuis:" -ForegroundColor White
Write-Host "   - Localhost: http://127.0.0.1:8000" -ForegroundColor White
Write-Host "   - Réseau: http://$ipAddress:8000" -ForegroundColor White
Write-Host ""

# Démarrer le serveur
php artisan serve --host=0.0.0.0 --port=8000







