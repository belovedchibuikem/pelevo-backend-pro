<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listening_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('episode_id')->constrained()->onDelete('cascade');
            $table->integer('duration_listened');
            $table->decimal('earnings', 10, 2)->default(0);
            $table->string('ip_address');
            $table->boolean('is_eligible_for_earnings')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listening_history');
    }
}; 