@echo off
chcp 65001 >nul
echo ========================================
echo   TEST CRÉATION DE COMPTE MOBILE
echo ========================================
echo.

set TIMESTAMP=%RANDOM%
set EMAIL=testuser%TIMESTAMP%@example.com
set PHONE=+22670%RANDOM%

echo Test avec les données:
echo   Email: %EMAIL%
echo   Téléphone: %PHONE%
echo.

curl -X POST "http://127.0.0.1:8000/api/v1/auth/register" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -H "X-Mobile-App: true" ^
  --data-raw "{\"nom\":\"TestNom\",\"prenom\":\"TestPrenom\",\"email\":\"%EMAIL%\",\"numero_telephone\":\"%PHONE%\",\"quartier\":\"Akpakpa\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}" ^
  --max-time 60 ^
  -v

echo.
echo.
echo ========================================
echo   Verification dans la base
echo ========================================
php artisan tinker --execute="echo '\nDerniers utilisateurs créés:' . PHP_EOL; \App\Models\User::latest()->limit(3)->get(['id', 'nom', 'prenom', 'email', 'status', 'created_at'])->each(function(\$u) { echo \"  - ID: {\$u->id} | {\$u->prenom} {\$u->nom} | {\$u->email} | Status: {\$u->status} | Créé: {\$u->created_at}\" . PHP_EOL; });"

echo.
pause
