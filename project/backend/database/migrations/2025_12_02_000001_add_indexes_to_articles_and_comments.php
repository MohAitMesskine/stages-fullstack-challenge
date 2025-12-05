<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Check existing indexes via information_schema to avoid duplicates
        $authorIdxExists = $this->indexExists('articles', 'articles_author_id_index');
        $publishedIdxExists = $this->indexExists('articles', 'articles_published_at_index');
        $commentsIdxExists = $this->indexExists('comments', 'comments_article_id_index');

        Schema::table('articles', function (Blueprint $table) use ($authorIdxExists, $publishedIdxExists) {
            if (!Schema::hasColumn('articles', 'author_id')) {
                $table->unsignedBigInteger('author_id')->nullable();
            }
            if (!$authorIdxExists) {
                $table->index('author_id', 'articles_author_id_index');
            }
            if (!Schema::hasColumn('articles', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }
            if (!$publishedIdxExists) {
                $table->index('published_at', 'articles_published_at_index');
            }
        });

        Schema::table('comments', function (Blueprint $table) use ($commentsIdxExists) {
            if (!Schema::hasColumn('comments', 'article_id')) {
                $table->unsignedBigInteger('article_id')->nullable();
            }
            if (!$commentsIdxExists) {
                $table->index('article_id', 'comments_article_id_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if ($this->indexExists('articles', 'articles_author_id_index')) {
                $table->dropIndex('articles_author_id_index');
            }
            if ($this->indexExists('articles', 'articles_published_at_index')) {
                $table->dropIndex('articles_published_at_index');
            }
        });
        Schema::table('comments', function (Blueprint $table) {
            if ($this->indexExists('comments', 'comments_article_id_index')) {
                $table->dropIndex('comments_article_id_index');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $dbName = DB::getDatabaseName();
        $count = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->count();
        return $count > 0;
    }
};
