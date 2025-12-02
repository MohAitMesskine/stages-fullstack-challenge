# Script de diagnostic pour l'installation
Write-Host "=== Diagnostic de l'installation ===" -ForegroundColor Cyan

Write-Host "`n[1] Vérification des containers..." -ForegroundColor Yellow
docker ps --filter "name=blog" --format "table {{.Names}}\t{{.Status}}"

Write-Host "`n[2] Vérification du dossier vendor..." -ForegroundColor Yellow
docker-compose exec -T backend sh -c "test -d vendor && echo '✅ vendor existe' || echo '❌ vendor manquant'"

Write-Host "`n[3] Vérification de autoload.php..." -ForegroundColor Yellow
docker-compose exec -T backend sh -c "test -f vendor/autoload.php && echo '✅ autoload.php existe' || echo '❌ autoload.php manquant'"

Write-Host "`n[4] Vérification des packages Laravel..." -ForegroundColor Yellow
docker-compose exec -T backend sh -c "test -d vendor/laravel && echo '✅ Laravel installé' || echo '❌ Laravel manquant'"

Write-Host "`n[5] Nombre de packages installés..." -ForegroundColor Yellow
docker-compose exec -T backend sh -c "ls vendor/ 2>/dev/null | wc -l"

Write-Host "`n[6] Vérification du fichier .env..." -ForegroundColor Yellow
docker-compose exec -T backend sh -c "test -f .env && echo '✅ .env existe' || echo '❌ .env manquant'"

Write-Host "`n[7] Test de PHP..." -ForegroundColor Yellow
docker-compose exec -T backend php -v

Write-Host "`n=== Diagnostic terminé ===" -ForegroundColor Cyan

Write-Host "`n💡 Prochaines étapes si tout est OK:" -ForegroundColor Green
Write-Host "   1. php artisan key:generate --force" -ForegroundColor White
Write-Host "   2. php artisan migrate --force" -ForegroundColor White
Write-Host "   3. Tester l'application sur http://localhost:8000" -ForegroundColor White
