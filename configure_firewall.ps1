# Script pour configurer le pare-feu Windows pour Laravel
# Exécuter en tant qu'administrateur

Write-Host "🔥 Configuration du pare-feu Windows pour Laravel" -ForegroundColor Cyan
Write-Host ""

# Vérifier si le script est exécuté en tant qu'administrateur
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "❌ Ce script doit être exécuté en tant qu'administrateur!" -ForegroundColor Red
    Write-Host "   Clic droit > Exécuter en tant qu'administrateur" -ForegroundColor Yellow
    pause
    exit 1
}

Write-Host "✅ Droits administrateur confirmés" -ForegroundColor Green
Write-Host ""

# Vérifier si la règle existe déjà
$existingRule = Get-NetFirewallRule -DisplayName "Laravel Dev Server" -ErrorAction SilentlyContinue

if ($existingRule) {
    Write-Host "⚠️  Une règle existe déjà. Suppression de l'ancienne règle..." -ForegroundColor Yellow
    Remove-NetFirewallRule -DisplayName "Laravel Dev Server" -ErrorAction SilentlyContinue
}

# Créer la nouvelle règle
try {
    New-NetFirewallRule -DisplayName "Laravel Dev Server" `
        -Direction Inbound `
        -LocalPort 8000 `
        -Protocol TCP `
        -Action Allow `
        -Description "Autorise les connexions au serveur Laravel pour développement Android"
    
    Write-Host "✅ Règle de pare-feu créée avec succès!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📱 Configuration complète:" -ForegroundColor Cyan
    Write-Host "   - Port: 8000" -ForegroundColor White
    Write-Host "   - Protocole: TCP" -ForegroundColor White
    Write-Host "   - Direction: Entrant" -ForegroundColor White
    Write-Host "   - Action: Autoriser" -ForegroundColor White
    Write-Host ""
    Write-Host "✅ Votre serveur Laravel est maintenant accessible depuis Android!" -ForegroundColor Green
} catch {
    Write-Host "❌ Erreur lors de la création de la règle: $_" -ForegroundColor Red
    Write-Host "   Essayez de créer la règle manuellement dans le Pare-feu Windows" -ForegroundColor Yellow
}

Write-Host ""
pause





