<?php

namespace App\Services;

use App\Models\Podcast;
use App\Models\Episode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaddyService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.taddy.api_url'); // Ensure this matches your .env and config/services.php
        $this->apiKey = config('services.taddy.api_key');
    }

    /**
     * Search for podcasts on Taddy API.
     *
     * @param string $query
     * @return array
     * @throws \Exception
     */
    public function searchPodcasts(string $query): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/podcasts', [
            'query' => $query,
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error('Taddy API Search Error: ' . $response->body() . ' for query: ' . $query);
            throw new \Exception('Failed to search podcasts: ' . $response->body());
        }
    }

    /**
     * Get details for a specific podcast from Taddy API.
     *
     * @param string $taddyId
     * @return array
     * @throws \Exception
     */
    public function getPodcastDetails(string $taddyId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/podcasts/' . $taddyId);

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error('Taddy API Podcast Details Error: ' . $response->body() . ' for ID: ' . $taddyId);
            throw new \Exception('Failed to get podcast details: ' . $response->body());
        }
    }

    /**
     * Get episodes for a specific podcast from Taddy API.
     *
     * @param string $taddyId
     * @return array
     * @throws \Exception
     */
    public function getPodcastEpisodes(string $taddyId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/podcasts/' . $taddyId . '/episodes');

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error('Taddy API Episodes Error: ' . $response->body() . ' for ID: ' . $taddyId);
            throw new \Exception('Failed to get podcast episodes: ' . $response->body());
        }
    }

    public function syncPodcasts()
    {
        // This method needs to be re-evaluated. Taddy API search endpoint does not support pagination for general listing.
        // If you need to sync all podcasts, you might need to iterate through categories or use a different endpoint
        // if available. For now, this will fetch a limited number of podcasts based on a generic query.
        
        try {
            // Example: Search for a broad term to get some podcasts
            $podcasts = $this->searchPodcasts('programming'); // Using a generic query for syncing
            
            if (empty($podcasts['results'])) { // Assuming 'results' key based on searchPodcasts return
                Log::info('No podcasts found to sync.');
                return;
            }

            foreach ($podcasts['results'] as $podcastData) {
                // Fetch full details for each podcast to ensure all fields are available
                $fullPodcastData = $this->getPodcastDetails($podcastData['id']);

                Podcast::updateOrCreate(
                    ['taddy_id' => $fullPodcastData['id']],
                    [
                        'title' => $fullPodcastData['title'] ?? 'N/A',
                        'description' => $fullPodcastData['description'] ?? 'N/A',
                        'image_url' => $fullPodcastData['image_url'] ?? 'N/A',
                        'author' => $fullPodcastData['author'] ?? 'N/A',
                        // Assuming user_id can be null or assigned later based on a system user
                        // For now, let's set a default or null if no user is associated at sync time
                        'user_id' => null, // Or a specific user ID if applicable
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Error syncing podcasts: ' . $e->getMessage());
        }
    }

    public function syncEpisodes(Podcast $podcast)
    {
        try {
            $episodes = $this->getPodcastEpisodes($podcast->taddy_id);
            
            if (empty($episodes['results'])) { // Assuming 'results' key
                Log::info('No episodes found to sync for podcast: ' . $podcast->title);
                return;
            }

            foreach ($episodes['results'] as $episodeData) {
                Episode::updateOrCreate(
                    ['taddy_episode_id' => $episodeData['id']],
                    [
                        'podcast_id' => $podcast->id,
                        'title' => $episodeData['title'] ?? 'N/A',
                        'description' => $episodeData['description'] ?? 'N/A',
                        'audio_url' => $episodeData['audio_url'] ?? 'N/A',
                        'duration' => $episodeData['duration'] ?? 0,
                        'published_at' => $episodeData['published_at'] ?? now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Error syncing episodes for podcast ' . $podcast->title . ': ' . $e->getMessage());
        }
    }
} 