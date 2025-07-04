<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('podcast_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('podcast_id')->constrained()->onDelete('cascade');
            $table->boolean('notify_new_episodes')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'podcast_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcast_subscriptions');
    }
}; 