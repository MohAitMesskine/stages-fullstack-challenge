<?php

/**
 * Script pour créer automatiquement des commentaires (version non-interactive)
 * 
 * Utilisation:
 * docker-compose exec backend php create_comments_auto.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Article;
use App\Models\User;
use App\Models\Comment;
use Faker\Factory as Faker;

echo "🚀 Création automatique de commentaires...\n\n";

$faker = Faker::create('fr_FR');

// Récupérer tous les articles
$articles = Article::all();
echo "📊 Trouvé {$articles->count()} articles\n";

// Récupérer tous les utilisateurs
$users = User::all();
echo "👥 Trouvé {$users->count()} utilisateurs\n\n";

if ($articles->isEmpty()) {
    echo "❌ Aucun article trouvé. Créez d'abord des articles.\n";
    exit(1);
}

if ($users->isEmpty()) {
    echo "❌ Aucun utilisateur trouvé. Créez d'abord des utilisateurs.\n";
    exit(1);
}

$totalCreated = 0;

foreach ($articles as $article) {
    // Nombre aléatoire de commentaires entre 3 et 8
    $numComments = rand(3, 8);
    
    echo "📝 Article #{$article->id} - '{$article->title}': création de {$numComments} commentaires...\n";
    
    for ($i = 0; $i < $numComments; $i++) {
        // Sélectionner un utilisateur aléatoire
        $user = $users->random();
        
        // Générer un commentaire réaliste
        $commentType = rand(1, 5);
        
        switch ($commentType) {
            case 1:
                $content = $faker->realText(rand(80, 200));
                break;
            case 2:
                $content = "Super article ! " . $faker->sentence(rand(5, 15));
                break;
            case 3:
                $content = "Merci pour ces informations. " . $faker->paragraph(rand(1, 3));
                break;
            case 4:
                $content = $faker->sentence(rand(8, 20)) . " " . $faker->sentence(rand(5, 12));
                break;
            default:
                $content = $faker->paragraph(rand(1, 2));
        }
        
        // Créer le commentaire
        Comment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'content' => $content,
            'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
        ]);
        
        $totalCreated++;
    }
    
    echo "   ✅ {$numComments} commentaires créés\n";
}

echo "\n🎉 Terminé ! Total: {$totalCreated} commentaires créés pour {$articles->count()} articles.\n";
echo "💡 Consultez http://localhost:8000/api/articles pour voir les résultats.\n";
