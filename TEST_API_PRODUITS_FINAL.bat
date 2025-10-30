@echo off
chcp 65001 >nul
echo ========================================
echo   TEST COMPLET DE L'API PRODUITS
echo ========================================
echo.

REM Trouver l'IP actuelle
echo Détection de l'IP actuelle...
setlocal enabledelayedexpansion
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr "10."') do (
    set ip=%%a
    set ip=!ip:~1!
    goto :found_ip
)
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr "192.168"') do (
    set ip=%%a
    set ip=!ip:~1!
    goto :found_ip
)

:found_ip
if not defined ip (
    echo ❌ Impossible de trouver votre adresse IP. Utilisez 127.0.0.1 par défaut.
    set ip=127.0.0.1
)

echo ✅ IP détectée: %ip%
echo.

set API_TEST_URL=http://%ip%:8000/api/v1/products/test
set API_PRODUCTS_URL=http://%ip%:8000/api/v1/products

echo ========================================
echo   TEST 1: Endpoint de Test
echo ========================================
echo.
echo URL: %API_TEST_URL%
echo.

where curl >nul 2>&1
if %errorlevel% equ 0 (
    echo Utilisation de curl...
    curl -X GET "%API_TEST_URL%" -H "Accept: application/json" -H "X-Mobile-App: true"
) else (
    echo Utilisation de Invoke-WebRequest (PowerShell)...
    powershell -Command "try { $response = Invoke-WebRequest -Uri '%API_TEST_URL%' -Headers @{'Accept'='application/json'; 'X-Mobile-App'='true'} -UseBasicParsing; Write-Host $response.Content } catch { Write-Host 'Erreur:' $_.Exception.Message }"
)

echo.
echo.
echo ========================================
echo   TEST 2: Endpoint Produits (sans pagination)
echo ========================================
echo.
echo URL: %API_PRODUCTS_URL%
echo.

where curl >nul 2>&1
if %errorlevel% equ 0 (
    echo Utilisation de curl...
    curl -X GET "%API_PRODUCTS_URL%?per_page=5" -H "Accept: application/json" -H "X-Mobile-App: true"
) else (
    echo Utilisation de Invoke-WebRequest (PowerShell)...
    powershell -Command "try { $response = Invoke-WebRequest -Uri '%API_PRODUCTS_URL%?per_page=5' -Headers @{'Accept'='application/json'; 'X-Mobile-App'='true'} -UseBasicParsing; Write-Host $response.Content } catch { Write-Host 'Erreur:' $_.Exception.Message }"
)

echo.
echo.
echo ========================================
echo   FIN DU TEST
echo ========================================
echo.
echo Si vous voyez "success":true et des produits dans "data", l'API fonctionne.
echo Si vous voyez des erreurs, vérifiez les logs Laravel dans storage\logs\laravel.log
echo.
pause

