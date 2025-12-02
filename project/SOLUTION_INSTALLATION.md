# SOLUTION COMPLÈTE - Installation pas à pas

## ⚠️ Le problème
Le fichier `vendor/autoload.php` n'existe pas car Composer n'a pas terminé l'installation.

## ✅ Solution étape par étape

### Étape 1 : Vérifier l'état actuel
```powershell
# Ouvrez un NOUVEAU terminal PowerShell et exécutez :
cd c:\Users\PC\Desktop\projet_void\stages-fullstack-challenge\project

# Vérifiez que les containers tournent
docker ps
```

### Étape 2 : Réinstaller Composer COMPLÈTEMENT
```powershell
# Supprimez le vendor incomplet
docker-compose exec backend rm -rf vendor

# Nettoyez le cache Composer
docker-compose exec backend composer clear-cache

# Réinstallez TOUT (cela prendra 5-7 minutes)
docker-compose exec backend composer install --no-interaction --optimize-autoloader

# ⏳ ATTENDEZ que cette commande se termine complètement
# Vous verrez : "Generating optimized autoload files" à la fin
```

### Étape 3 : Vérifier que l'installation est complète
```powershell
# Cette commande doit retourner "OK"
docker-compose exec backend sh -c "test -f vendor/autoload.php && echo 'OK' || echo 'ERREUR'"

# Vérifiez Laravel
docker-compose exec backend sh -c "test -d vendor/laravel/framework && echo 'Laravel OK' || echo 'Laravel manquant'"
```

### Étape 4 : Configuration Laravel
```powershell
# Copier .env
docker-compose exec backend sh -c "test -f .env || cp .env.example .env"

# Générer la clé
docker-compose exec backend php artisan key:generate --force

# ✅ Si cette commande fonctionne, vous pouvez continuer
```

### Étape 5 : Exécuter les migrations (BUG-001)
```powershell
# Exécuter toutes les migrations
docker-compose exec backend php artisan migrate --force

# Vérifier le statut
docker-compose exec backend php artisan migrate:status
```

### Étape 6 : Tester
```powershell
# Tester Laravel
docker-compose exec backend php artisan --version

# Ouvrir dans le navigateur
# Frontend: http://localhost:3000
# Backend: http://localhost:8000
```

## 🔧 Si ça ne marche toujours pas

### Option A : Rebuild complet
```powershell
# Arrêter tout
docker-compose down

# Rebuild les images
docker-compose build --no-cache backend

# Redémarrer
docker-compose up -d

# Réinstaller Composer (étape 2)
docker-compose exec backend composer install --no-interaction --optimize-autoloader

# Puis étapes 3, 4, 5
```

### Option B : Installation locale (si Docker pose problème)
```powershell
# Entrer dans le container
docker-compose exec backend sh

# Une fois dans le container :
cd /var/www/html
rm -rf vendor
composer install --no-interaction
composer dump-autoload --optimize

# Vérifier
ls -la vendor/autoload.php

# Si OK, sortir du container
exit

# Puis continuer avec l'étape 4
```

## 📝 Résumé de ce qui a été corrigé (BUG-001)

Une fois les migrations exécutées, voici ce qui sera appliqué :

### Fichiers modifiés :
1. **Migration** : `backend/database/migrations/2024_12_02_000001_fix_articles_table_collation_for_accent_search.php`
   - Change `latin1_general_ci` → `utf8mb4_unicode_ci`
   - Permet la recherche sans accents

2. **Controller** : `backend/app/Http/Controllers/ArticleController.php`
   - SQL brut vulnérable → Eloquent sécurisé
   - Recherche dans title ET content

3. **Config** : `backend/config/database.php`
   - `utf8_general_ci` → `utf8mb4_unicode_ci`

### Test de la correction :
```powershell
# Une fois que tout fonctionne, testez via l'API :

# Créer un article avec accent (via l'interface ou l'API)
# Titre: "Le café du matin"

# Rechercher sans accent
Invoke-WebRequest -Uri "http://localhost:8000/api/articles/search?q=cafe" -Method GET

# Rechercher avec accent
Invoke-WebRequest -Uri "http://localhost:8000/api/articles/search?q=café" -Method GET

# Les deux doivent retourner le même résultat ✅
```

## ⏱️ Temps estimé
- Étape 2 (composer install) : 5-7 minutes
- Étapes 3-6 : 2-3 minutes
- **Total : ~10 minutes**

## 💡 Conseil
Ouvrez un **nouveau terminal PowerShell** et exécutez ces commandes une par une.
N'exécutez pas la commande suivante tant que la précédente n'est pas terminée !
