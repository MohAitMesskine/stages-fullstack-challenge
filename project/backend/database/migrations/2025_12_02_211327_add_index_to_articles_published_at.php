<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Only add the index if it doesn't already exist
        $indexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', 'articles')
            ->where('index_name', 'articles_published_at_index')
            ->exists();

        if (!$indexExists && Schema::hasColumn('articles', 'published_at')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->index('published_at', 'articles_published_at_index');
            });
        }
    }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down(): void
        {
            // Only drop the index if it exists
            $indexExists = DB::table('information_schema.statistics')
                ->where('table_schema', DB::raw('DATABASE()'))
                ->where('table_name', 'articles')
                ->where('index_name', 'articles_published_at_index')
                ->exists();

            if ($indexExists) {
                Schema::table('articles', function (Blueprint $table) {
                    $table->dropIndex('articles_published_at_index');
                });
            }
        }
    };
