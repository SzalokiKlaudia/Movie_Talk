<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users
        DB::statement('ALTER TABLE users ADD CONSTRAINT check_birth_year CHECK (birth_year <= ' . now()->year . ')');

        // Movies
        DB::statement("ALTER TABLE movies ADD CONSTRAINT check_release_date CHECK (release_date <= " . today()->toDateString() . ")"); //check constraint a mgjelenés évre
        DB::statement("ALTER TABLE movies ADD CONSTRAINT check_duration_minutes CHECK (duration_minutes > 0)"); // ck add a hány percre

        // User movies
        DB::statement('ALTER TABLE user_movies ADD CONSTRAINT check_rating CHECK (rating > 0 AND rating <= 5 OR rating IS NULL)');

        // Forum topics
        DB::statement("ALTER TABLE forum_topics ADD CONSTRAINT check_created_topics CHECK (created_at <= '". Carbon::now() ."')");

        // Forum comments
        DB::statement("ALTER TABLE forum_comments ADD CONSTRAINT check_created_comments CHECK (created_at <= '". Carbon::now() ."')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
