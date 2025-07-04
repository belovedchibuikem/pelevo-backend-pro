<?php
// app/Http/Resources/EpisodeResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'name' => $this->resource['name'],
            'description' => $this->resource['description'],
            'duration_ms' => $this->resource['duration_ms'],
            'explicit' => $this->resource['explicit'] ?? false,
            'external_urls' => $this->resource['external_urls'] ?? [],
            'href' => $this->resource['href'],
            'html_description' => $this->resource['html_description'] ?? '',
            'images' => $this->resource['images'] ?? [],
            'is_externally_hosted' => $this->resource['is_externally_hosted'] ?? false,
            'is_playable' => $this->resource['is_playable'] ?? true,
            'language' => $this->resource['language'] ?? 'en',
            'languages' => $this->resource['languages'] ?? [],
            'release_date' => $this->resource['release_date'],
            'release_date_precision' => $this->resource['release_date_precision'] ?? 'day',
            'type' => $this->resource['type'],
            'uri' => $this->resource['uri'],
            'audio_preview_url' => $this->resource['audio_preview_url'] ?? null,
            'resume_point' => $this->resource['resume_point'] ?? null
        ];
    }
}