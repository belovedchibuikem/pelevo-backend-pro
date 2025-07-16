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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // This is a new migration file for podcasts table
        Schema::create('podcasts', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // PodcastIndex API id
            $table->string('title');
            $table->string('url');
            $table->string('original_url')->nullable();
            $table->string('link')->nullable();
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('image')->nullable();
            $table->string('artwork')->nullable();
            $table->unsignedBigInteger('last_update_time')->nullable();
            $table->unsignedBigInteger('last_crawl_time')->nullable();
            $table->unsignedBigInteger('last_parse_time')->nullable();
            $table->boolean('in_polling_queue')->default(false);
            $table->integer('priority')->nullable();
            $table->unsignedBigInteger('last_good_http_status_time')->nullable();
            $table->integer('last_http_status')->nullable();
            $table->string('content_type')->nullable();
            $table->unsignedBigInteger('itunes_id')->nullable();
            $table->string('generator')->nullable();
            $table->string('language', 10)->nullable();
            $table->integer('type')->nullable();
            $table->boolean('dead')->default(false);
            $table->integer('crawl_errors')->default(0);
            $table->integer('parse_errors')->default(0);
            $table->json('categories')->nullable();
            $table->boolean('locked')->default(false);
            $table->boolean('explicit')->default(false);
            $table->uuid('podcast_guid')->nullable();
            $table->string('medium')->nullable();
            $table->integer('episode_count')->default(0);
            $table->unsignedBigInteger('image_url_hash')->nullable();
            $table->unsignedBigInteger('newest_item_pubdate')->nullable();
            $table->timestamps();
        });

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('podcasts');
        Schema::dropIfExists('podcast_episodes');
    }
};
