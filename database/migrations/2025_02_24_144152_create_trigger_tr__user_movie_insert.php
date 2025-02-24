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
            CREATE TRIGGER tr_UserMovie_Insert
            AFTER INSERT ON user_movies
            FOR EACH ROW
            BEGIN
                
                DECLARE counting INT;

                SELECT COUNT(*) INTO counting
                FROM user_movies
                WHERE user_id = NEW.user_id AND movie_id = NEW.movie_id AND (watching_date IS NULL OR rating IS NULL);

                IF counting > 1 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid insert: duplicate or missing data';
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trigger_tr__user_movie_insert');
    }
};
