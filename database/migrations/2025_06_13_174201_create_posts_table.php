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
        Schema::create('posts', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('user_id');
            $table->text('caption')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('post_type', ['text', 'image', 'image_with_caption'])->default('text');
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('saves_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
