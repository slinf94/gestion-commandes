@echo off
echo ========================================
echo   CORRECTION DES ERREURS DE MIGRATION
echo ========================================
echo.

echo [1/5] Nettoyage de la base de données...
php artisan db:wipe
if %errorlevel% neq 0 (
    echo Tentative avec migrate:reset...
    php artisan migrate:reset
)

echo.
echo [2/5] Nettoyage du cache...
php artisan config:clear
php artisan cache:clear

echo.
echo [3/5] Vérification des migrations...
php artisan migrate:status

echo.
echo [4/5] Exécution des migrations...
php artisan migrate:fresh --seed

echo.
echo [5/5] Vérification finale...
php artisan migrate:status

echo.
echo ========================================
echo   CORRECTION TERMINEE !
echo ========================================
echo.
echo Si des erreurs persistent, contactez l'administrateur.
pause
