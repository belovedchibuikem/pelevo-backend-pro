<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayHistoryResource extends JsonResource
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
            'episode_id' => $this->episode_id,
            'progress_seconds' => $this->progress_seconds,
            'status' => $this->status,
            'last_played_at' => $this->last_played_at?->toISOString(),
            'episode' => [
                'id' => $this->episode->id,
                'title' => $this->episode->title,
                'description' => $this->episode->description,
                'duration' => $this->episode->duration,
                'duration_formatted' => $this->episode->duration_formatted,
                'pub_date' => $this->episode->pub_date?->toISOString(),
                'image' => $this->episode->image,
                'podcast' => [
                    'id' => $this->episode->podcast->id,
                    'title' => $this->episode->podcast->title,
                    'author' => $this->episode->podcast->author,
                    'image' => $this->episode->podcast->image,
                ]
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
