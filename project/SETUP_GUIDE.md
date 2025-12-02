# Guide de Setup et Migration - Blog Platform

## 🚀 Démarrage rapide

### Prérequis
- Docker & Docker Compose installés
- Ports 3000, 8000 et 3306 disponibles

### 1. Démarrer les containers

```powershell
cd project
docker-compose up -d
```

### 2. Attendre que l'installation Composer se termine

Cela peut prendre 2-5 minutes lors du premier démarrage.

```powershell
# Vérifier si Composer a terminé
docker-compose exec backend sh -c "test -f vendor/autoload.php && echo 'OK' || echo 'Pas encore prêt'"
```

Si "Pas encore prêt", attendez encore 1-2 minutes et réessayez.

### 3. Configuration de Laravel

```powershell
# Copier le fichier .env
docker-compose exec backend sh -c "test -f .env || cp .env.example .env"

# Générer la clé d'application
docker-compose exec backend php artisan key:generate --force

# Exécuter les migrations
docker-compose exec backend php artisan migrate --force
```

### 4. Accéder à l'application

- **Backend API** : http://localhost:8000
- **Frontend React** : http://localhost:3000
- **Base de données** : localhost:3306 (user: root, password: secret)

## ✅ Vérification de l'installation

```powershell
# Vérifier la version de Laravel
docker-compose exec backend php artisan --version

# Vérifier que les migrations sont appliquées
docker-compose exec backend php artisan migrate:status

# Tester l'API
# PowerShell
Invoke-WebRequest -Uri http://localhost:8000/api/articles -Method GET
```

## 🔧 Commandes utiles

```powershell
# Voir les logs du backend
docker-compose logs -f backend

# Voir les logs du frontend
docker-compose logs -f frontend

# Entrer dans le container backend
docker-compose exec backend sh

# Arrêter les containers
docker-compose down

# Redémarrer les containers
docker-compose restart

# Rebuild complet
docker-compose down
docker-compose up -d --build
```

## 🐛 [BUG-001] Correction de la recherche avec accents

### Problème résolu
La recherche ne fonctionnait pas avec les accents car la table `articles` utilisait la collation `latin1_general_ci`.

### Solution implémentée
1. **Migration** : `2024_12_02_000001_fix_articles_table_collation_for_accent_search.php`
   - Convertit la table vers `utf8mb4_unicode_ci`
   - Préserve toutes les données existantes

2. **Code sécurisé** : `ArticleController::search()`
   - Utilise Eloquent au lieu de SQL brut
   - Corrige la faille d'injection SQL

3. **Configuration** : `config/database.php`
   - Charset: `utf8mb4`
   - Collation: `utf8mb4_unicode_ci`

### Test de la correction

```powershell
# La migration s'exécute automatiquement avec : php artisan migrate
# Après la migration, testez via l'interface ou l'API

# Test via API (après avoir créé un article "Le café du matin")
Invoke-WebRequest -Uri "http://localhost:8000/api/articles/search?q=cafe" -Method GET
Invoke-WebRequest -Uri "http://localhost:8000/api/articles/search?q=café" -Method GET
```

Les deux requêtes devraient maintenant retourner le même résultat ! ✅

## 📝 Points importants

- La première installation prend 5-10 minutes (téléchargement des dépendances)
- Les installations suivantes sont beaucoup plus rapides (cache Docker)
- Si vous rencontrez des erreurs, vérifiez que les ports ne sont pas déjà utilisés
- Les données sont persistées dans des volumes Docker

## 🆘 Dépannage

### "vendor/autoload.php not found"
→ L'installation Composer n'est pas terminée, attendez encore 1-2 minutes

### "Connection refused" sur l'API
→ Vérifiez que le container backend est bien démarré : `docker ps`

### "Database connection error"
→ Attendez que MySQL soit complètement démarré (30 secondes après `docker-compose up`)

### Tout réinstaller proprement
```powershell
docker-compose down -v
docker-compose up -d --build
# Puis réexécutez les étapes 2 et 3
```
