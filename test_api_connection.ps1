# Script de test de connexion API pour mobile
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  TEST DE CONNEXION API MOBILE" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Obtenir l'IP locale
$ipConfig = ipconfig | Select-String "IPv4"
$ip = ($ipConfig -split ':')[1].Trim()
Write-Host "IP detectee: $ip" -ForegroundColor Yellow
Write-Host ""

# URLs de test
$baseUrl = "http://$ip:8000"
$apiUrl = "$baseUrl/api/v1"
$pingUrl = "$apiUrl/ping"

Write-Host "URLs de test:" -ForegroundColor Green
Write-Host "  Base: $baseUrl" -ForegroundColor White
Write-Host "  API: $apiUrl" -ForegroundColor White
Write-Host "  Ping: $pingUrl" -ForegroundColor White
Write-Host ""

# Test 1: Vérifier si le serveur répond
Write-Host "Test 1: Verification du serveur..." -ForegroundColor Cyan
try {
    $response = Invoke-WebRequest -Uri "$baseUrl/api/ping" -Method GET -TimeoutSec 5 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "  [OK] Serveur accessible" -ForegroundColor Green
        $response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 3
    }
} catch {
    Write-Host "  [ERREUR] Serveur inaccessible: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "  Verifiez que le serveur est demarre avec: php artisan serve --host=0.0.0.0 --port=8000" -ForegroundColor Yellow
    exit 1
}
Write-Host ""

# Test 2: Test de l'endpoint API ping
Write-Host "Test 2: Test endpoint API ping..." -ForegroundColor Cyan
try {
    $response = Invoke-WebRequest -Uri $pingUrl -Method GET -TimeoutSec 5 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "  [OK] API ping fonctionne" -ForegroundColor Green
        $data = $response.Content | ConvertFrom-Json
        Write-Host "  Message: $($data.message)" -ForegroundColor White
        Write-Host "  Server IP: $($data.server_ip)" -ForegroundColor White
    }
} catch {
    Write-Host "  [ERREUR] API ping echoue: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 3: Test CORS
Write-Host "Test 3: Verification CORS..." -ForegroundColor Cyan
try {
    $headers = @{
        'Origin' = 'http://localhost'
        'Access-Control-Request-Method' = 'POST'
        'Access-Control-Request-Headers' = 'Content-Type'
    }
    $response = Invoke-WebRequest -Uri $pingUrl -Method OPTIONS -Headers $headers -TimeoutSec 5 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "  [OK] CORS configure" -ForegroundColor Green
        Write-Host "  Headers CORS:" -ForegroundColor White
        $response.Headers | Where-Object { $_.Key -like '*Access-Control*' } | ForEach-Object {
            Write-Host "    $($_.Key): $($_.Value)" -ForegroundColor Gray
        }
    }
} catch {
    Write-Host "  [ERREUR] Test CORS echoue: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 4: Test de connexion depuis differentes IPs
Write-Host "Test 4: Test depuis l'IP locale..." -ForegroundColor Cyan
$testUrls = @(
    "http://localhost:8000/api/v1/ping",
    "http://127.0.0.1:8000/api/v1/ping",
    "http://$ip:8000/api/v1/ping"
)

foreach ($url in $testUrls) {
    try {
        $response = Invoke-WebRequest -Uri $url -Method GET -TimeoutSec 3 -UseBasicParsing
        Write-Host "  [OK] $url" -ForegroundColor Green
    } catch {
        Write-Host "  [ERREUR] $url - $($_.Exception.Message)" -ForegroundColor Red
    }
}
Write-Host ""

# Résumé
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  CONFIGURATION POUR APPLICATION MOBILE" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Mettez a jour ces fichiers dans l'application mobile:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. gestion_commandes_mobile/lib/core/constants/app_constants.dart" -ForegroundColor White
Write-Host "   Remplacer: http://10.152.173.8:8000" -ForegroundColor Gray
Write-Host "   Par:       http://$ip:8000" -ForegroundColor Green
Write-Host ""
Write-Host "2. gestion_commandes_mobile/lib/core/config/app_config.dart" -ForegroundColor White
Write-Host "   Remplacer: http://10.152.173.8:8000" -ForegroundColor Gray
Write-Host "   Par:       http://$ip:8000" -ForegroundColor Green
Write-Host ""
Write-Host "3. Redemarrer l'application mobile" -ForegroundColor Yellow
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
