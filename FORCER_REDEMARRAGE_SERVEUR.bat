@echo off
chcp 65001 >nul
echo ========================================
echo   FORCE REDEMARRAGE SERVEUR LARAVEL
echo   POUR ACCES MOBILE
echo ========================================
echo.

echo [1/5] Arret de tous les processus PHP...
taskkill /F /IM php.exe >nul 2>&1
timeout /t 3 /nobreak >nul
echo OK - Processus PHP arretes
echo.

echo [2/5] Verification des ports...
netstat -ano | findstr :8000 >nul
if %ERRORLEVEL% EQU 0 (
    echo ATTENTION: Le port 8000 est encore utilise!
    echo Tentative d'liberation...
    for /f "tokens=5" %%a in ('netstat -ano ^| findstr :8000') do (
        taskkill /F /PID %%a >nul 2>&1
    )
    timeout /t 2 /nobreak >nul
)
echo OK - Port 8000 libre
echo.

echo [3/5] Detection de l'IP actuelle...
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr "10."') do (
    set ip=%%a
    set ip=!ip:~1!
    goto :found1
)

for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr "192.168"') do (
    set ip=%%a
    set ip=!ip:~1!
    goto :found1
)

:found1
if not defined ip (
    echo ERREUR: Impossible de trouver l'IP
    pause
    exit /b 1
)

echo OK - IP detectee: %ip%
echo.

echo [4/5] Configuration du serveur...
echo.
echo ========================================
echo   INFORMATIONS IMPORTANTES
echo ========================================
echo.
echo IP du serveur: %ip%
echo URL de l'API: http://%ip%:8000/api/v1
echo.
echo Le serveur va demarrer sur 0.0.0.0:8000
echo Cela permet l'acces depuis le telephone mobile
echo.
echo ========================================
echo.

echo [5/5] Demarrage du serveur...
echo.
echo ATTENTION: Le serveur doit demarrer sur 0.0.0.0:8000
echo Si vous voyez 127.0.0.1:8000, ARRETEZ et redemarrez!
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo.

REM Lancer le serveur sur toutes les interfaces
php artisan serve --host=0.0.0.0 --port=8000

