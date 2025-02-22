<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_movies', function (Blueprint $table) {
            $table->softDeletes(); // így adja hozzá a deleted_at a táblához

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_movies', function (Blueprint $table) {
            $table->dropSoftDeletes(); // ha visszavonom törli a deleted_at-et a táblából

        });
    }
};
