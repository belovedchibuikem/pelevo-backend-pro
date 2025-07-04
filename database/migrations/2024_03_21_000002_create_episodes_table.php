<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('podcast_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('audio_url');
            $table->integer('duration')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('image')->nullable();
            $table->boolean('explicit')->default(false);
            $table->integer('episode_number')->nullable();
            $table->integer('season_number')->nullable();
            $table->string('guid')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
}; 