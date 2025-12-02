# Script de setup et migration pour le projet Blog
Write-Host "=== Setup du projet Blog ===" -ForegroundColor Green

# 1. Installation des dépendances Composer
Write-Host "`n[1/5] Installation des dépendances PHP..." -ForegroundColor Yellow
docker-compose exec -T backend composer install --no-interaction --optimize-autoloader
if ($LASTEXITCODE -ne 0) {
    Write-Host "Erreur lors de l'installation Composer" -ForegroundColor Red
    exit 1
}

# 2. Copie du fichier .env
Write-Host "`n[2/5] Configuration du fichier .env..." -ForegroundColor Yellow
docker-compose exec -T backend sh -c "if [ ! -f .env ]; then cp .env.example .env; echo '.env créé'; else echo '.env existe déjà'; fi"

# 3. Génération de la clé d'application
Write-Host "`n[3/5] Génération de la clé Laravel..." -ForegroundColor Yellow
docker-compose exec -T backend php artisan key:generate --force

# 4. Exécution des migrations
Write-Host "`n[4/5] Exécution des migrations de base de données..." -ForegroundColor Yellow
docker-compose exec -T backend php artisan migrate --force

# 5. Vérification
Write-Host "`n[5/5] Vérification de l'installation..." -ForegroundColor Yellow
docker-compose exec -T backend php artisan --version

Write-Host "`n=== Setup terminé ! ===" -ForegroundColor Green
Write-Host "`nLe backend est accessible sur: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Le frontend est accessible sur: http://localhost:3000" -ForegroundColor Cyan
Write-Host "`nPour tester la correction du BUG-001 (recherche avec accents):" -ForegroundColor Yellow
Write-Host "  - La migration a changé la collation de latin1_general_ci vers utf8mb4_unicode_ci" -ForegroundColor White
Write-Host "  - La recherche est maintenant insensible aux accents" -ForegroundColor White
Write-Host "  - Rechercher 'cafe' trouvera 'café' et vice-versa" -ForegroundColor White
