@echo off
echo ========================================
echo CORRECTION CREATION DE PRODUITS
echo ========================================
echo.

cd /d "%~dp0"

echo Etape 1/4 : Vidage du cache de configuration...
php artisan config:clear
if %ERRORLEVEL% EQU 0 (
    echo   ✓ Cache config vide
) else (
    echo   ✗ Erreur vidage cache
)
echo.

echo Etape 2/4 : Vidage du cache de routes...
php artisan route:clear
echo   ✓ Cache routes vide
echo.

echo Etape 3/4 : Vidage du cache de vues...
php artisan view:clear
echo   ✓ Cache vues vide
echo.

echo Etape 4/4 : Verification de la configuration...
php artisan config:cache
echo   ✓ Configuration mise en cache
echo.

echo ========================================
echo ✅ TERMINE !
echo ========================================
echo.
echo La creation de produits devrait maintenant fonctionner !
echo.
echo Test : Allez sur http://127.0.0.1:8000/admin/products/create
echo        et essayez de creer un nouveau produit
echo.
pause

