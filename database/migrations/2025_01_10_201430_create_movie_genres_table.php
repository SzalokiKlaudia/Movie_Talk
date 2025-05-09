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
        Schema::create('movie_genres', function (Blueprint $table) {
            $table->id();//alap típusa az id-nek nagyobb értéket vesz fel mint az int
            $table->unsignedBigInteger('movie_id'); //alap típusa az id-nek nagyobb értéket vesz fel mint az int
            $table->unsignedBigInteger('genre_id');
            $table->timestamps();

            $table->foreign('movie_id')->references('id')->on('movies');
            $table->foreign('genre_id')->references('id')->on('genres');

            $table->unique(['movie_id', 'genre_id']); //összetett unique értékek hogy ne legyen redundancia

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_genres');
    }
};
