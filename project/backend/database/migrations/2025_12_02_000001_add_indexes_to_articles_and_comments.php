<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'author_id')) {
                // Ensure column exists in older schemas
                $table->unsignedBigInteger('author_id')->nullable()->index();
            } else {
                $table->index('author_id', 'articles_author_id_index');
            }
            $table->index('published_at', 'articles_published_at_index');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index('article_id', 'comments_article_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_author_id_index');
            $table->dropIndex('articles_published_at_index');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_article_id_index');
        });
    }
};
