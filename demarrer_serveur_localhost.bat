@echo off
echo ===========================================
echo   DEMARRAGE SERVEUR LARAVEL POUR CHROME
echo ===========================================
echo.
echo Le serveur sera accessible sur:
echo   - http://127.0.0.1:8000
echo   - http://localhost:8000
echo.
echo Ouvrez Chrome et allez sur: http://localhost:8000
echo.
echo ===========================================
echo.

cd /d "%~dp0"
php artisan serve --host=127.0.0.1 --port=8000

pause




