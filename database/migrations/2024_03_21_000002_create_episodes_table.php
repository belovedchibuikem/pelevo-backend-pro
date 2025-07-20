<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      
           // Migration for podcast_episodes table
        Schema::create('episodes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // API episode id
            $table->string('title');
            $table->string('link')->nullable();
            $table->text('description')->nullable();
            $table->uuid('guid')->nullable();
            $table->unsignedBigInteger('date_published')->nullable();
            $table->string('date_published_pretty')->nullable();
            $table->unsignedBigInteger('date_crawled')->nullable();
            $table->string('enclosure_url')->nullable();
            $table->string('enclosure_type')->nullable();
            $table->unsignedBigInteger('enclosure_length')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('explicit')->default(false);
            $table->integer('episode')->nullable();
            $table->string('episode_type')->nullable();
            $table->integer('season')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('feed_itunes_id')->nullable();
            $table->string('feed_url')->nullable();
            $table->string('feed_image')->nullable();
            $table->unsignedBigInteger('feed_id'); // PodcastIndex podcast id
            $table->uuid('podcast_guid')->nullable();
            $table->string('feed_language', 10)->nullable();
            $table->boolean('feed_dead')->default(false);
            $table->unsignedBigInteger('feed_duplicate_of')->nullable();
            $table->string('chapters_url')->nullable();
            $table->string('transcript_url')->nullable();
            $table->timestamps();

            $table->foreign('feed_id')->references('id')->on('podcasts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
}; 