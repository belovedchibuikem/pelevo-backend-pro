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
            $table->string('show_id');
            $table->string('show_name')->nullable();
            $table->text('show_description')->nullable();
            $table->string('show_image_url')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('notify_new_episodes')->default(true);
            $table->timestamps();
            
            $table->unique(['user_id', 'show_id']);
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcast_subscriptions');
    }
};
