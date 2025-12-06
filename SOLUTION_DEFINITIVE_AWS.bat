@echo off
cls
echo ========================================
echo SOLUTION DEFINITIVE - ERREUR AWS S3
echo ========================================
echo.

cd /d "%~dp0"

echo ETAPE 1/6 : Suppression du cache de configuration...
if exist "bootstrap\cache\config.php" (
    del /F /Q "bootstrap\cache\config.php"
    echo   ✓ Fichier cache supprime
) else (
    echo   - Pas de fichier cache
)
echo.

echo ETAPE 2/6 : Vidage de tous les caches Laravel...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo   ✓ Tous les caches vides
echo.

echo ETAPE 3/6 : Verification de la configuration...
php artisan tinker --execute="echo 'Filesystem actuel : ' . config('filesystems.default') . PHP_EOL;"
echo.

echo ETAPE 4/6 : Si vous voyez 'r2' ou 's3' ci-dessus, PROBLEME !
echo             Si vous voyez 'public', c'est BON !
echo.
pause
echo.

echo ETAPE 5/6 : Arretez le serveur dans l'autre terminal
echo             (Appuyez sur Ctrl+C dans le terminal du serveur)
echo.
echo Appuyez sur une touche quand c'est fait...
pause >nul
echo.

echo ETAPE 6/6 : Redemarrez le serveur
echo             Allez dans l'autre terminal et tapez :
echo             php artisan serve
echo.
echo ========================================
echo ✅ TERMINE !
echo ========================================
echo.
echo MAINTENANT :
echo 1. Redemarrez le serveur (php artisan serve)
echo 2. Testez la modification d'un produit avec image
echo 3. Ca devrait fonctionner !
echo.
pause
