#!/usr/bin/env php
<?php

/**
 * Script pour ajouter des index de performance sur la table articles
 * Exécuter avec: docker-compose exec backend php add_indexes.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 Ajout des index de performance...\n\n";

try {
    // Vérifier si les index existent déjà
    $indexes = DB::select("SHOW INDEX FROM articles WHERE Key_name IN ('idx_articles_published_created', 'idx_articles_author')");
    
    if (count($indexes) > 0) {
        echo "✅ Les index existent déjà!\n";
        foreach ($indexes as $index) {
            echo "   - {$index->Key_name}\n";
        }
    } else {
        echo "📊 Création des index...\n";
        
        // Index pour orderByDesc('published_at')->orderByDesc('created_at')
        DB::statement('CREATE INDEX idx_articles_published_created ON articles(published_at DESC, created_at DESC)');
        echo "   ✅ Index idx_articles_published_created créé\n";
        
        // Index pour les jointures avec author
        DB::statement('CREATE INDEX idx_articles_author ON articles(author_id)');
        echo "   ✅ Index idx_articles_author créé\n";
        
        // Optimiser la table
        DB::statement('OPTIMIZE TABLE articles');
        echo "   ✅ Table articles optimisée\n";
    }
    
    echo "\n🎉 Terminé! Rechargez votre page pour voir l'amélioration.\n";
    echo "💡 La requête DB devrait passer de ~1100ms à <100ms\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
