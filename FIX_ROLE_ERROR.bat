@echo off
echo ========================================
echo CORRECTION DE L'ERREUR DE LA COLONNE ROLE
echo ========================================
echo.

cd /d "%~dp0"

echo Execution du script de correction...
echo.
php fix_role_column.php

echo.
echo ========================================
echo MAINTENANT, RELANCEZ LE SEEDER :
echo ========================================
echo.
echo Commande a executer :
echo php artisan db:seed --class=UserSeeder
echo.
pause

