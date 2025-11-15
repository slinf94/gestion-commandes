@echo off
chcp 65001 >nul
echo ========================================
echo   DÉMARRAGE SERVEUR POUR APPLICATION MOBILE
echo ========================================
echo.

REM Arrêter les serveurs existants
echo [1/4] Arrêt des serveurs PHP existants...
taskkill /F /IM php.exe 2>nul
timeout /t 2 /nobreak >nul

REM Trouver l'IP actuelle
echo [2/4] Détection de l'adresse IP...
set ip=
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr "10."') do (
    set ip=%%a
    goto :found_ip
)
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr "192.168"') do (
    set ip=%%a
    goto :found_ip
)

:found_ip
set ip=%ip: =%
if "%ip%"=="" (
    echo ❌ Impossible de trouver l'adresse IP
    echo Utilisation de 127.0.0.1 par défaut (MOBILE NE FONCTIONNERA PAS)
    set ip=127.0.0.1
) else (
    echo ✅ IP détectée: %ip%
)
echo.

echo [3/4] Configuration...
echo.
echo ════════════════════════════════════════════
echo   📱 URL API MOBILE
echo ════════════════════════════════════════════
echo   http://%ip%:8000/api/v1
echo.
echo ════════════════════════════════════════════
echo   🌐 URL ADMIN (Navigateur)
echo ════════════════════════════════════════════
echo   http://127.0.0.1:8000/admin
echo   OU
echo   http://%ip%:8000/admin
echo.
echo ════════════════════════════════════════════

REM Changer vers le dossier du projet
cd /d "%~dp0"

echo [4/4] Démarrage du serveur...
echo.
echo ⚠️  Le serveur va démarrer sur 0.0.0.0:8000 (accessible depuis le réseau)
echo ⚠️  Appuyez sur Ctrl+C pour arrêter le serveur
echo.
echo ════════════════════════════════════════════
echo.

REM Démarrer le serveur avec timeout augmenté
php -d max_execution_time=300 -d memory_limit=512M artisan serve --host=0.0.0.0 --port=8000

pause















