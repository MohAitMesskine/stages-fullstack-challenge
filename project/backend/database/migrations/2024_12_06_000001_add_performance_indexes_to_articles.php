<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPerformanceIndexesToArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ajouter des index pour optimiser les requêtes de liste d'articles
        DB::statement('CREATE INDEX idx_articles_published_created ON articles(published_at DESC, created_at DESC)');
        DB::statement('CREATE INDEX idx_articles_author ON articles(author_id)');
        
        // Optimiser la table
        DB::statement('OPTIMIZE TABLE articles');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX idx_articles_published_created ON articles');
        DB::statement('DROP INDEX idx_articles_author ON articles');
    }
}
