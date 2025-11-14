# Test simple de l'API
Write-Host "Test de l'API..." -ForegroundColor Cyan

$ip = "10.193.46.8"
$url = "http://$ip:8000/api/v1/ping"

Write-Host "URL: $url" -ForegroundColor Yellow

try {
    $response = Invoke-RestMethod -Uri $url -Method GET -Headers @{"Accept"="application/json"} -ErrorAction Stop
    Write-Host "[OK] API fonctionne!" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "[ERREUR] $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Status: $($_.Exception.Response.StatusCode)" -ForegroundColor Red
}










