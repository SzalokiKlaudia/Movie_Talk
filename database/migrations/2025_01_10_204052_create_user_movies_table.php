<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('movie_id'); //alap típusa az id-nek nagyobb értéket vesz fel mint az int
            $table->tinyInteger('rating')->nullable()->default(null);
            $table->date('watching_date');
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('movie_id')->references('id')->on('movies');

            $table->unique(['user_id', 'movie_id','watching_date']); 
        });
        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE user_movies DROP CONSTRAINT check_rating');

        // A tábla eltávolítása
        Schema::dropIfExists('user_movies');
    }
};
