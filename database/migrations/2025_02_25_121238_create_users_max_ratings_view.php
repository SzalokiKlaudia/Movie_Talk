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
        DB::statement("
            CREATE VIEW UsersMaxRatings AS
            SELECT 
                s.user_id, 
                s.movie_id, 
                MAX(s.rating) AS max_rating
            FROM user_movies s
            WHERE s.rating IS NOT NULL
            GROUP BY s.user_id, s.movie_id;
        ");
    }

  
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_max_ratings_view');
    }
};
