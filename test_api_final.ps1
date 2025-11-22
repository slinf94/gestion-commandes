# Test final de l'API
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  TEST API MOBILE" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$ip = "10.193.46.8"
$url = "http://${ip}:8000/api/v1/ping"

Write-Host "IP: $ip" -ForegroundColor Yellow
Write-Host "URL: $url" -ForegroundColor Yellow
Write-Host ""

try {
    $response = Invoke-RestMethod -Uri $url -Method GET -Headers @{"Accept"="application/json"} -ErrorAction Stop
    Write-Host "[OK] API fonctionne!" -ForegroundColor Green
    Write-Host ""
    $response | ConvertTo-Json -Depth 3
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "  CONFIGURATION MOBILE" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Mettez cette URL dans l'application mobile:" -ForegroundColor Yellow
    Write-Host "http://$ip:8000/api/v1" -ForegroundColor Green
    Write-Host ""
} catch {
    Write-Host "[ERREUR]" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    if ($_.Exception.Response) {
        Write-Host "Status: $($_.Exception.Response.StatusCode)" -ForegroundColor Red
    }
}



















