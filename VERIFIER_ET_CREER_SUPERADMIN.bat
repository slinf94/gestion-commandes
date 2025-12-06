@echo off
cls
echo ========================================
echo VERIFICATION ET CREATION SUPER ADMIN
echo ========================================
echo.

cd /d "%~dp0"

echo ETAPE 1 : Verification du compte...
php artisan tinker --execute="use App\Models\User; \$user = User::where('email', 'superadmin@allomobile.com')->first(); if (\$user) { echo 'Compte existe : ID ' . \$user->id . ', Role: ' . \$user->role; } else { echo 'COMPTE N EXISTE PAS'; } echo PHP_EOL; exit;"
echo.

echo ETAPE 2 : Modification de la colonne role...
php creer_superadmin.php
echo.

echo ========================================
echo VERIFICATION FINALE
echo ========================================
echo.

php artisan tinker --execute="use App\Models\User; \$user = User::where('email', 'superadmin@allomobile.com')->first(); if (\$user) { echo '✓ Compte super admin cree !'; echo PHP_EOL; echo 'Email : ' . \$user->email; echo PHP_EOL; echo 'Role : ' . \$user->role; echo PHP_EOL; echo 'Status : ' . \$user->status; } else { echo '✗ ECHEC : Compte non cree'; } echo PHP_EOL; exit;"
echo.

echo ========================================
echo INFORMATIONS DE CONNEXION
echo ========================================
echo.
echo Email       : superadmin@allomobile.com
echo Mot de passe : SuperAdmin123!
echo URL         : http://127.0.0.1:8000/admin/login
echo.
echo Essayez de vous connecter maintenant !
echo.
pause
