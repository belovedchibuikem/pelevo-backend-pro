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
        Schema::create('play_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('podcastindex_episode_id');
            $table->enum('status', ['played', 'paused'])->default('played');
            $table->integer('position')->default(0); // playback position in seconds
            $table->timestamps();
            $table->unique(['user_id', 'podcastindex_episode_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_history');
    }
};
