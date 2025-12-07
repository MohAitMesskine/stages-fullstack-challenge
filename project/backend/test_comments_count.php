<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Article;

echo "🔍 Test du comments_count...\n\n";

$article = Article::withCount('comments')->first();

echo "Article ID: {$article->id}\n";
echo "Titre: {$article->title}\n";
echo "Comments Count: {$article->comments_count}\n\n";

echo "✅ Si vous voyez un nombre > 0, le SQL fonctionne !\n";
