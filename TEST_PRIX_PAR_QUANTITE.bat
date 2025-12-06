@echo off
echo ========================================
echo TEST RAPIDE - PRIX PAR QUANTITE
echo ========================================
echo.
echo Ce script va tester la fonctionnalite des prix par quantite.
echo.
pause

cd /d "%~dp0"

echo.
echo [1/3] Verification de la migration...
php artisan migrate:status | findstr product_prices

echo.
echo [2/3] Test API - Recuperation d'un produit avec quantity_prices...
echo.
curl -X GET "http://127.0.0.1:8000/api/products/1" -H "Accept: application/json"

echo.
echo.
echo [3/3] Ouverture de l'interface admin...
start http://127.0.0.1:8000/admin/products

echo.
echo ========================================
echo ETAPES DE TEST MANUEL
echo ========================================
echo.
echo 1. Dans l'interface admin, selectionnez un produit
echo 2. Cliquez sur le bouton jaune "Prix par Quantite"
echo 3. Ajoutez des paliers de prix :
echo    - Quantite Min: 1, Max: 9, Prix: 1000, Remise: 0%%
echo    - Quantite Min: 10, Max: 49, Prix: 900, Remise: 10%%
echo    - Quantite Min: 50, Max: (vide), Prix: 800, Remise: 20%%
echo.
echo 4. Testez dans l'app mobile ou via l'API
echo.
echo ========================================
echo TEST TERMINE !
echo ========================================
pause
