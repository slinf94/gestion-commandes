@echo off
echo ========================================
echo  DEMARRAGE SERVEUR LARAVEL POUR MOBILE
echo ========================================
echo.

REM Obtenir l'IP locale
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    set IP=%%a
    goto :found
)
:found
set IP=%IP:~1%
echo IP detectee: %IP%
echo.

REM Vérifier si le port 8000 est déjà utilisé
netstat -an | findstr :8000 >nul
if %errorlevel% == 0 (
    echo ATTENTION: Le port 8000 est deja utilise!
    echo.
    echo Voulez-vous arreter le processus existant? (O/N)
    set /p stop="> "
    if /i "%stop%"=="O" (
        echo Arret des processus sur le port 8000...
        for /f "tokens=5" %%a in ('netstat -ano ^| findstr :8000 ^| findstr LISTENING') do (
            taskkill /F /PID %%a >nul 2>&1
        )
        timeout /t 2 >nul
    )
)

echo.
echo Demarrage du serveur Laravel...
echo URL: http://%IP%:8000
echo API: http://%IP%:8000/api/v1
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause
