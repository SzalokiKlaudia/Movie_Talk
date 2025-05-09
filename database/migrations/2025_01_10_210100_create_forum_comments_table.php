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
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_topic_id');
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->dateTime('created_at');

            $table->foreign('forum_topic_id')->references('id')->on('forum_topics');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE forum_comments DROP CONSTRAINT check_created_at');

        Schema::dropIfExists('forum_comments');
    }
};
