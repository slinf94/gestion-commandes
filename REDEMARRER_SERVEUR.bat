@echo off
echo ========================================
echo   REDEMARRAGE DU SERVEUR LARAVEL
echo ========================================
echo.

echo [1/3] Vidage des caches Laravel...
php artisan clear-compiled
php artisan cache:clear
php artisan route:clear
php artisan config:clear

echo.
echo [2/3] Verification de la syntaxe PHP...
php -l app/Http/Controllers/Admin/ProductController.php

echo.
echo [3/3] Demarrage du serveur...
echo.
echo ========================================
echo   SERVEUR DEMARRE SUR http://127.0.0.1:8000
echo   Appuyez sur Ctrl+C pour arreter
echo ========================================
echo.

php artisan serve
