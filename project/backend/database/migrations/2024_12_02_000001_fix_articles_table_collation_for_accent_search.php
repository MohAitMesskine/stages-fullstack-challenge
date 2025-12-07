<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixArticlesTableCollationForAccentSearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modifier la collation de la table et des colonnes sans perdre les données
        DB::statement('ALTER TABLE articles CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        
        // S'assurer que les colonnes spécifiques utilisent la bonne collation
        DB::statement('ALTER TABLE articles MODIFY title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE articles MODIFY content TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE articles MODIFY image_path VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revenir à l'ancienne collation (latin1)
        DB::statement('ALTER TABLE articles CONVERT TO CHARACTER SET latin1 COLLATE latin1_general_ci');
        
        DB::statement('ALTER TABLE articles MODIFY title VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_ci');
        DB::statement('ALTER TABLE articles MODIFY content TEXT CHARACTER SET latin1 COLLATE latin1_general_ci');
        DB::statement('ALTER TABLE articles MODIFY image_path VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_ci NULL');
    }
}
