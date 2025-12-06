@echo off
echo ========================================
echo SOLUTION FINALE - ERREUR S3/IMAGES
echo ========================================
echo.

cd /d "%~dp0"

echo IMPORTANT : Arretez le serveur PHP dans l'autre terminal
echo            (Appuyez sur Ctrl+C dans le terminal du serveur)
echo.
echo Appuyez sur une touche quand c'est fait...
pause >nul
echo.

echo Etape 1/7 : Suppression de tous les fichiers de cache...
if exist "bootstrap\cache\config.php" (
    del /F /Q "bootstrap\cache\config.php"
    echo   ✓ Cache config supprime
) else (
    echo   - Pas de cache config
)
echo.

echo Etape 2/7 : Vidage cache configuration...
php artisan config:clear
echo   ✓ Cache config vide
echo.

echo Etape 3/7 : Vidage cache application...
php artisan cache:clear
echo   ✓ Cache application vide
echo.

echo Etape 4/7 : Vidage cache vues...
php artisan view:clear
echo   ✓ Cache vues vide
echo.

echo Etape 5/7 : Vidage cache routes...
php artisan route:clear
echo   ✓ Cache routes vide
echo.

echo Etape 6/7 : Verification de la configuration...
php artisan tinker --execute="echo 'FILESYSTEM_DISK actuel : ' . config('filesystems.default') . PHP_EOL;"
echo.

echo Etape 7/7 : Redemarrage du serveur...
echo.
echo ========================================
echo ✅ TERMINE !
echo ========================================
echo.
echo MAINTENANT :
echo 1. Dans l'autre terminal, relancez le serveur :
echo    php artisan serve
echo.
echo 2. Allez sur : http://127.0.0.1:8000/admin/products/create
echo.
echo 3. Essayez de creer un produit avec une image
echo.
echo 4. Ca devrait fonctionner !
echo.
pause

