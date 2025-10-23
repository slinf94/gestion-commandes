@echo off
echo ========================================
echo   SERVEUR LARAVEL POUR APPLICATION MOBILE
echo ========================================
echo.
echo Demarrage du serveur accessible depuis l'app mobile...
echo.
echo URL de l'API: http://192.168.100.73:8000/api/v1
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo.
php artisan serve --host=0.0.0.0 --port=8000



