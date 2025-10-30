@echo off
chcp 65001 >nul
echo ========================================
echo   TEST DE COMMUNICATION API
echo ========================================
echo.

REM Récupérer l'IP actuelle
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "192.168.*IPv4"') do (
    set ip=%%a
    goto :found
)

for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr "192.168"') do (
    set ip=%%a
    set ip=!ip:~1!
    goto :found
)

:found
if not defined ip (
    echo ❌ Impossible de trouver votre adresse IP WiFi
    echo.
    echo Exécutez: ipconfig ^| findstr /i "IPv4"
    pause
    exit /b 1
)

echo ✅ Votre IP WiFi détectée: %ip%
echo.
echo ========================================
echo   TEST 1: Vérifier que le serveur répond
echo ========================================
echo.
echo Test de l'endpoint products...
echo URL: http://%ip%:8000/api/v1/products
echo.

curl -s -o nul -w "Statut HTTP: %%{http_code}\n" http://%ip%:8000/api/v1/products

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ Le serveur répond correctement !
    echo.
) else (
    echo.
    echo ❌ Le serveur ne répond pas ou n'est pas démarré
    echo.
    echo Vérifiez que le serveur Laravel est démarré avec:
    echo   start_server_mobile.bat
    echo.
)

echo ========================================
echo   TEST 2: Vérifier depuis le navigateur
echo ========================================
echo.
echo Ouvrez cette URL dans votre navigateur:
echo   http://%ip%:8000/api/v1/products
echo.
echo Vous devriez voir une réponse JSON.
echo.

echo ========================================
echo   TEST 3: Vérifier depuis l'application mobile
echo ========================================
echo.
echo 1. Ouvrez l'application Flutter
echo 2. Essayez de charger les produits
echo 3. Si ça fonctionne, la communication est OK ✅
echo.

pause

