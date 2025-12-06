@echo off
cls
echo ========================================
echo CORRECTION COMPLETE ERREUR AWS S3
echo ========================================
echo.

cd /d "%~dp0"

echo ETAPE 1 : Arret du serveur...
echo.
echo IMPORTANT : Allez dans le terminal du serveur
echo             et appuyez sur Ctrl+C pour l'arreter
echo.
echo Appuyez sur une touche quand c'est fait...
pause >nul
echo.

echo ETAPE 2 : Suppression de TOUS les fichiers de cache...
if exist "bootstrap\cache\config.php" del /F /Q "bootstrap\cache\config.php"
if exist "bootstrap\cache\routes-v7.php" del /F /Q "bootstrap\cache\routes-v7.php"
if exist "bootstrap\cache\services.php" del /F /Q "bootstrap\cache\services.php"
echo   ✓ Fichiers cache supprimes
echo.

echo ETAPE 3 : Vidage des caches Laravel...
call php artisan config:clear
call php artisan cache:clear
call php artisan route:clear
call php artisan view:clear
echo   ✓ Caches Laravel vides
echo.

echo ETAPE 4 : Verification de la configuration...
echo.
call php artisan tinker --execute="echo 'Config filesystem : ' . config('filesystems.default') . PHP_EOL; exit;"
echo.

echo ETAPE 5 : Test de la configuration...
call php artisan tinker --execute="echo 'Test Storage::disk() : '; try { \$disk = Storage::disk(); echo 'Driver = ' . \$disk->getAdapter()->getPathPrefix(); } catch (\Exception \$e) { echo 'Erreur : ' . \$e->getMessage(); } echo PHP_EOL; exit;"
echo.

echo ========================================
echo ✅ TERMINE !
echo ========================================
echo.
echo MAINTENANT :
echo.
echo 1. Dans le terminal du serveur, tapez :
echo    php artisan serve
echo.
echo 2. Attendez que le serveur demarre
echo.
echo 3. Testez la modification d'un produit
echo.
echo Si l'erreur persiste, il faut modifier le fichier .env
echo.
pause
