<?php
// app/Http/Resources/PodcastResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'name' => $this->resource['name'],
            'description' => $this->resource['description'],
            'publisher' => $this->resource['publisher'],
            'images' => $this->resource['images'] ?? [],
            'total_episodes' => $this->resource['total_episodes'] ?? 0,
            'languages' => $this->resource['languages'] ?? [],
            'explicit' => $this->resource['explicit'] ?? false,
            'type' => $this->resource['type'],
            'uri' => $this->resource['uri'],
            'external_urls' => $this->resource['external_urls'] ?? [],
            'href' => $this->resource['href'],
            'is_externally_hosted' => $this->resource['is_externally_hosted'] ?? false,
            'media_type' => $this->resource['media_type'] ?? 'audio'
        ];
    }
}