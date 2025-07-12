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
                'description' => $this->podcast->description,
                'author' => $this->podcast->author,
                'image' => $this->podcast->image,
                'episode_count' => $this->podcast->episode_count,
                'categories' => $this->podcast->categories,
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
