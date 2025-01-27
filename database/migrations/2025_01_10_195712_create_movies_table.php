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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title',40); //varchar(40)
            $table->text('description');
            $table->date('release_date');
            $table->integer('duration_minutes');
            $table->string('image_url');
            $table->string('trailer_url');
            $table->string('cast_url');
            $table->timestamps();
        });

        


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE movies DROP CONSTRAINT check_release_date"); //constraintek eldobása
        DB::statement("ALTER TABLE movies DROP CONSTRAINT check_duration_minutes");

        Schema::dropIfExists('movies');

    }
};
