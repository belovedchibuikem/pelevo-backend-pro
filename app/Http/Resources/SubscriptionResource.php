<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'podcast_id' => $this->podcast_id,
            'subscribed_at' => $this->subscribed_at?->toISOString(),
            'unsubscribed_at' => $this->unsubscribed_at?->toISOString(),
            'is_active' => $this->is_active,
            'podcast' => [
                'id' => $this->podcast->id,
                'title' => $this->podcast->title,
                'url' => $this->podcast->url,
                'original_url' => $this->podcast->original_url,
                'link' => $this->podcast->link,
                'description' => $this->podcast->description,
                'author' => $this->podcast->author,
                'owner_name' => $this->podcast->owner_name,
                'image' => $this->podcast->image,
                'artwork' => $this->podcast->artwork,
                'last_update_time' => $this->podcast->last_update_time,
                'last_crawl_time' => $this->podcast->last_crawl_time,
                'last_parse_time' => $this->podcast->last_parse_time,
                'in_polling_queue' => $this->podcast->in_polling_queue,
                'priority' => $this->podcast->priority,
                'last_good_http_status_time' => $this->podcast->last_good_http_status_time,
                'last_http_status' => $this->podcast->last_http_status,
                'content_type' => $this->podcast->content_type,
                'itunes_id' => $this->podcast->itunes_id,
                'generator' => $this->podcast->generator,
                'language' => $this->podcast->language,
                'type' => $this->podcast->type,
                'dead' => $this->podcast->dead,
                'crawl_errors' => $this->podcast->crawl_errors,
                'parse_errors' => $this->podcast->parse_errors,
                'categories' => $this->podcast->categories,
                'locked' => $this->podcast->locked,
                'explicit' => $this->podcast->explicit,
                'podcast_guid' => $this->podcast->podcast_guid,
                'medium' => $this->podcast->medium,
                'episode_count' => $this->podcast->episode_count,
                'image_url_hash' => $this->podcast->image_url_hash,
                'newest_item_pubdate' => $this->podcast->newest_item_pubdate,
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
