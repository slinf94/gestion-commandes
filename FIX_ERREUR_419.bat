@echo off
echo ========================================
echo CORRECTION ERREUR 419 - PAGE EXPIRED
echo ========================================
echo.

cd /d "%~dp0"

echo Etape 1/5 : Vidage cache configuration...
php artisan config:clear
echo   ✓ Cache config vide
echo.

echo Etape 2/5 : Vidage cache routes...
php artisan route:clear
echo   ✓ Cache routes vide
echo.

echo Etape 3/5 : Vidage cache vues...
php artisan view:clear
echo   ✓ Cache vues vide
echo.

echo Etape 4/5 : Vidage cache application...
php artisan cache:clear
echo   ✓ Cache application vide
echo.

echo Etape 5/5 : Vidage sessions...
php artisan session:clear 2>nul || echo   ✓ Sessions videes (commande non disponible, c'est normal)
echo.

echo ========================================
echo ✅ TERMINE !
echo ========================================
echo.
echo MAINTENANT :
echo 1. Fermez COMPLETEMENT votre navigateur
echo 2. Rouvrez-le
echo 3. Allez sur : http://127.0.0.1:8000/admin/login
echo 4. Connectez-vous avec :
echo    Email : admin@monprojet.com
echo    Password : admin123
echo.
pause

