<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    
    }

    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
}; 