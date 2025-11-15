# Test complet de l'inscription API
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  TEST INSCRIPTION API" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$ip = "10.193.46.8"
$baseUrl = "http://${ip}:8000/api/v1"

# Test 1: Ping
Write-Host "Test 1: Ping API..." -ForegroundColor Yellow
try {
    $pingResponse = Invoke-RestMethod -Uri "${baseUrl}/ping" -Method GET -Headers @{"Accept"="application/json"} -ErrorAction Stop
    Write-Host "[OK] Ping: $($pingResponse.message)" -ForegroundColor Green
} catch {
    Write-Host "[ERREUR] Ping echoue: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Test 2: Test d'inscription
Write-Host "Test 2: Test inscription..." -ForegroundColor Yellow
$testEmail = "test_$(Get-Date -Format 'yyyyMMddHHmmss')@test.com"
$testPhone = "123456789$(Get-Random -Minimum 1000 -Maximum 9999)"

$registerData = @{
    nom = "Test"
    prenom = "User"
    email = $testEmail
    numero_telephone = $testPhone
    quartier = "Test Quartier"
    password = "password123"
    password_confirmation = "password123"
} | ConvertTo-Json

Write-Host "Donnees: $registerData" -ForegroundColor Gray
Write-Host ""

try {
    $startTime = Get-Date
    $response = Invoke-RestMethod -Uri "${baseUrl}/auth/register" -Method POST -Body $registerData -ContentType "application/json" -Headers @{"Accept"="application/json"; "X-Mobile-App"="true"} -TimeoutSec 300 -ErrorAction Stop
    $endTime = Get-Date
    $duration = ($endTime - $startTime).TotalSeconds
    
    Write-Host "[OK] Inscription reussie!" -ForegroundColor Green
    Write-Host "Duree: $duration secondes" -ForegroundColor Green
    Write-Host "Message: $($response.message)" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 3
} catch {
    $endTime = Get-Date
    $duration = ($endTime - $startTime).TotalSeconds
    Write-Host "[ERREUR] Inscription echoue apres $duration secondes" -ForegroundColor Red
    Write-Host "Message: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.Exception.Response) {
        $statusCode = $_.Exception.Response.StatusCode
        Write-Host "Status Code: $statusCode" -ForegroundColor Red
        
        try {
            $errorStream = $_.Exception.Response.GetResponseStream()
            $reader = New-Object System.IO.StreamReader($errorStream)
            $errorBody = $reader.ReadToEnd()
            Write-Host "Erreur detaillee:" -ForegroundColor Red
            Write-Host $errorBody -ForegroundColor Red
        } catch {
            Write-Host "Impossible de lire le corps de l'erreur" -ForegroundColor Yellow
        }
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan













