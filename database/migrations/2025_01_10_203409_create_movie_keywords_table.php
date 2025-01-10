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
        Schema::create('movie_keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id')->unique(); //alap típusa az id-nek nagyobb értéket vesz fel mint az int
            $table->unsignedBigInteger('keyword_id')->unique();
        

            $table->foreign('movie_id')->references('id')->on('movies');
            $table->foreign('keyword_id')->references('id')->on('keywords');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_keywords');
    }
};
