@echo off
echo ========================================
echo   TEST DE LA CREATION DE COMPTE
echo ========================================
echo.

echo Test 1: Verification de la route API...
curl -X GET http://127.0.0.1:8000/api/v1/auth/quartiers
echo.
echo.

echo Test 2: Creation de compte avec donnees minimales...
curl -X POST http://127.0.0.1:8000/api/v1/auth/register ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"nom\":\"Test\",\"prenom\":\"User\",\"email\":\"test%RANDOM%@example.com\",\"numero_telephone\":\"+22670123456\",\"quartier\":\"Akpakpa\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}"
echo.
echo.

echo Test 3: Verification dans la base de donnees...
php artisan tinker --execute="echo 'Derniers users:' . PHP_EOL; \App\Models\User::latest()->take(3)->get(['id', 'nom', 'prenom', 'email', 'status'])->each(function(\$u) { echo \"ID: {\$u->id}, Nom: {\$u->nom} {\$u->prenom}, Email: {\$u->email}, Status: {\$u->status}\" . PHP_EOL; });"

echo.
pause
