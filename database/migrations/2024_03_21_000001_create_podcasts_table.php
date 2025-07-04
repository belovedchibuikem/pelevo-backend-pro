<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->string('feed_id')->unique();
            $table->string('title');
            $table->string('url');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->string('language')->nullable();
            $table->json('categories')->nullable();
            $table->boolean('explicit')->default(false);
            $table->integer('episode_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
}; 