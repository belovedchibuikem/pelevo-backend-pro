<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DownloadResource extends JsonResource
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
            'file_path' => $this->file_path,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'formatted_file_size' => $this->formatted_file_size,
            'downloaded_at' => $this->downloaded_at?->toISOString(),
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
