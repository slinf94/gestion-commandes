@echo off
echo ========================================
echo   DEMARRAGE SERVEUR LARAVEL POUR ANDROID
echo ========================================
echo.
echo Cette commande demarre le serveur Laravel sur toutes les interfaces
echo pour permettre la connexion depuis un appareil Android physique.
echo.
echo IP detectee: 10.77.168.8
echo URL Android: http://10.77.168.8:8000
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo.
pause

php artisan serve --host=0.0.0.0 --port=8000

