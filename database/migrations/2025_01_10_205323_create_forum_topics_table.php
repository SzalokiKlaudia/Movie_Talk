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
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title',30)->unique();
            $table->dateTime('created_at');
            //$table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
        
         //nyers sql ckeck constraint
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE forum_topics DROP CONSTRAINT check_created_at');
        Schema::dropIfExists('forum_topics');
    }
};
