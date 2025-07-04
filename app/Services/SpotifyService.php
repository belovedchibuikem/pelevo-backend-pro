<?php
// app/Services/SpotifyService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use \Exception;

class SpotifyService
{
    protected $baseUrl = 'https://api.spotify.com/v1';
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id');
        $this->clientSecret = config('services.spotify.client_secret');
    }

    /**
     * Get access token using client credentials
     */
    public function getAccessToken(): string
    {
        return Cache::remember('spotify_access_token', 3600, function () {
            $response = Http::withoutVerifying()->asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post('https://accounts.spotify.com/api/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            throw new \Exception('Failed to get Spotify access token');
        });
    }

    /**
     * Get categories from Spotify
     */
    public function getCategories(string $country = 'US', int $limit = 20, int $offset = 0): array
    {
        $token = $this->getAccessToken();
        
        $response = Http::withoutVerifying()->withToken($token)
            ->get("{$this->baseUrl}/browse/categories", [
                'country' => $country,
                'limit' => $limit,
                'offset' => $offset
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $categories = [];
            
            if (isset($data['categories']['items'])) {
                foreach ($data['categories']['items'] as $item) {
                    // Map Spotify categories to podcast-friendly categories
                    $category = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'icon' => $item['icons'][0]['url'] ?? null,
                        'count' => 0, // We'll update this when we get podcasts
                        'gradientStart' => $this->getCategoryGradientStart($item['id']),
                        'gradientEnd' => $this->getCategoryGradientEnd($item['id']),
                    ];
                    $categories[] = $category;
                }
            }
            
            return $categories;
        }

        throw new \Exception('Failed to fetch categories');
    }

    /**
     * Get podcasts by category
     */
    public function getPodcastsByCategory(string $categoryId, array $options = []): string
    {
        $params = array_filter([
            'country' => $options['country'] ?? 'US',
            'limit' => $options['limit'] ?? 20,
            'offset' => $options['offset'] ?? 0,
        ]);

        return $this->makeRequest("/browse/categories/{$categoryId}/playlists", $params);
    }

    /**
     * Get gradient start color for a category
     */
    private function getCategoryGradientStart(string $categoryId): string
    {
        $gradients = [
            'toplists' => '0xFF1DB954', // Spotify green
            'pop' => '0xFFFF6B6B',
            'hiphop' => '0xFF6B66FF',
            'rock' => '0xFFFF6B6B',
            'indie' => '0xFF6BFF6B',
            'jazz' => '0xFF6BFFFF',
            'classical' => '0xFFFFB36B',
            'electronic' => '0xFFB36BFF',
            'r&b' => '0xFFFF6BB3',
            'metal' => '0xFF6B6B6B',
            'default' => '0xFF1DB954',
        ];

        return $gradients[$categoryId] ?? $gradients['default'];
    }

    /**
     * Get gradient end color for a category
     */
    private function getCategoryGradientEnd(string $categoryId): string
    {
        $gradients = [
            'toplists' => '0xFF1ED760', // Lighter Spotify green
            'pop' => '0xFFFF8E8E',
            'hiphop' => '0xFF8E8EFF',
            'rock' => '0xFFFF8E8E',
            'indie' => '0xFF8EFF8E',
            'jazz' => '0xFF8EFFFF',
            'classical' => '0xFFFFD18E',
            'electronic' => '0xFFD18EFF',
            'r&b' => '0xFFFF8ED1',
            'metal' => '0xFF8E8E8E',
            'default' => '0xFF1ED760',
        ];

        return $gradients[$categoryId] ?? $gradients['default'];
    }

    /**
     * Search for shows/podcasts
     */
    public function searchShows(string $query, int $limit = 20, int $offset = 0, string $market = 'US'): array
    {
        $token = $this->getAccessToken();
        
        $response = Http::withoutVerifying()->withToken($token)
            ->get("{$this->baseUrl}/search", [
                'q' => $query,
                'type' => 'show',
                'market' => $market,
                'limit' => $limit,
                'offset' => $offset
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to search shows');
    }

    /**
     * Get show details
     */
    public function getShow(string $showId, string $market = 'US'): array
    {
        $token = $this->getAccessToken();
        
        $response = Http::withoutVerifying()->withToken($token)
            ->get("{$this->baseUrl}/shows/{$showId}", [
                'market' => $market
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to fetch show details');
    }

    /**
     * Get episodes for a show
     */
    public function getShowEpisodes(string $showId, int $limit = 20, int $offset = 0, string $market = 'US'): array
    {
        $token = $this->getAccessToken();
        
        $response = Http::withoutVerifying()->withToken($token)
            ->get("{$this->baseUrl}/shows/{$showId}/episodes", [
                'market' => $market,
                'limit' => $limit,
                'offset' => $offset
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to fetch show episodes');
    }

    /**
     * Get episode details
     */
    public function getEpisode(string $episodeId, string $market = 'US'): array
    {
        $token = $this->getAccessToken();
        
        $response = Http::withoutVerifying()->withToken($token)
            ->get("{$this->baseUrl}/episodes/{$episodeId}", [
                'market' => $market
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to fetch episode details');
    }

    /**
     * Save shows to user's library
     */
    public function saveShows(array $showIds, string $userToken): bool
    {
        $response = Http::withoutVerifying()->withToken($userToken)
            ->put("{$this->baseUrl}/me/shows", [
                'ids' => implode(',', $showIds)
            ]);

        return $response->successful();
    }

    /**
     * Remove shows from user's library
     */
    public function removeShows(array $showIds, string $userToken): bool
    {
        $response = Http::withoutVerifying()->withToken($userToken)
            ->delete("{$this->baseUrl}/me/shows", [
                'ids' => implode(',', $showIds)
            ]);

        return $response->successful();
    }

    /**
     * Get user's saved shows
     */
    public function getUserSavedShows(string $userToken, int $limit = 20, int $offset = 0): array
    {
        $response = Http::withoutVerifying()->withToken($userToken)
            ->get("{$this->baseUrl}/me/shows", [
                'limit' => $limit,
                'offset' => $offset
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to fetch user saved shows');
    }

    /**
     * Get featured podcasts from Spotify
     */
    public function getFeaturedPodcasts(array $options = []): string
    {
        $token = $this->getAccessToken();
        
        // First get the featured playlists to find relevant podcasts
        $response = Http::withoutVerifying()->withToken($token)
            ->get("{$this->baseUrl}/browse/featured-playlists", [
                'country' => $options['country'] ?? 'US',
                'limit' => $options['limit'] ?? 5
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch featured playlists');
        }

        $featuredData = $response->json();
        $podcasts = [];

        // Search for podcasts in each featured playlist
        if (isset($featuredData['playlists']['items'])) {
            foreach ($featuredData['playlists']['items'] as $playlist) {
                try {
                    // Search for podcasts related to the playlist's theme
                    $searchResponse = Http::withoutVerifying()->withToken($token)
                        ->get("{$this->baseUrl}/search", [
                            'q' => $options['query'] ?? $playlist['name'],
                            'type' => 'show',
                            'market' => $options['country'] ?? 'US',
                            'limit' => 5
                        ]);

                    if ($searchResponse->successful()) {
                        $searchData = $searchResponse->json();
                        if (isset($searchData['shows']['items'])) {
                            foreach ($searchData['shows']['items'] as $show) {
                                // Format the podcast data
                                $podcast = [
                                    'id' => $show['id'],
                                    'title' => $show['name'],
                                    'creator' => $show['publisher'],
                                    'coverImage' => $show['images'][0]['url'] ?? '',
                                    'duration' => '0m', // Spotify doesn't provide duration for shows
                                    'description' => $show['description'] ?? '',
                                    'category' => $show['type'],
                                    'audioUrl' => $show['external_urls']['spotify'] ?? '',
                                    'totalEpisodes' => $show['total_episodes'] ?? 0,
                                    'languages' => $show['languages'] ?? [],
                                    'explicit' => $show['explicit'] ?? false,
                                    'isFeatured' => true,
                                ];
                                $podcasts[] = $podcast;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but continue with other playlists
                    Log::warning("Error fetching podcasts for playlist {$playlist['name']}: " . $e->getMessage());
                }
            }
        }

        // Remove duplicates based on podcast ID
        $uniquePodcasts = [];
        foreach ($podcasts as $podcast) {
            $uniquePodcasts[$podcast['id']] = $podcast;
        }

        // Return limited number of unique podcasts
        $response = [
            'podcasts' => array_slice(array_values($uniquePodcasts), 0, $options['limit'] ?? 20),
            'total' => count($uniquePodcasts),
            'limit' => $options['limit'] ?? 20,
            'offset' => $options['offset'] ?? 0,
        ];

        return json_encode($response);
    }

    /**
     * Get new episodes from subscribed shows
     */
    public function getNewEpisodesFromShows(array $showIds, int $limit = 50): array
    {
        $allEpisodes = [];
        $token = $this->getAccessToken();
        
        foreach (array_slice($showIds, 0, 10) as $showId) { // Limit to prevent too many API calls
            try {
                $episodes = $this->getShowEpisodes($showId, 5, 0); // Get 5 latest episodes per show
                if (isset($episodes['items'])) {
                    $allEpisodes = array_merge($allEpisodes, $episodes['items']);
                }
            } catch (\Exception $e) {
                Log::warning("Failed to fetch episodes for show {$showId}: " . $e->getMessage());
            }
        }

        // Sort by release date (newest first)
        usort($allEpisodes, function ($a, $b) {
            return strtotime($b['release_date']) - strtotime($a['release_date']);
        });

        return [
            'episodes' => array_slice($allEpisodes, 0, $limit)
        ];
    }

    /**
     * Get recommended podcasts for user
     */
    public function getRecommendedPodcasts(User $user, int $limit = 20): array
    {
        // Simplified recommendation logic - in production, this would be more sophisticated
        $recommendedTerms = ['technology', 'science', 'history', 'true crime', 'self improvement'];
        $randomTerm = $recommendedTerms[array_rand($recommendedTerms)];
        
        return $this->searchShows($randomTerm, $limit, 0, 'US');
    }

    public function getFeaturedPlaylists(array $options = []): string
    {
        $params = array_filter([
            'country' => $options['country'] ?? 'US',
            'limit' => $options['limit'] ?? 20,
            'offset' => $options['offset'] ?? 0,
        ]);

        return $this->makeRequest('/browse/featured-playlists', $params);
    }

    public function getNewPodcastReleases(array $options = []): string
    {
        $params = [
            'q' => 'tag:new',
            'type' => 'show',
            'market' => $options['country'] ?? 'US',
            'limit' => $options['limit'] ?? 20,
            'offset' => $options['offset'] ?? 0,
        ];

        $result = $this->makeRequest('/search', $params);
        $data = json_decode($result, true);
        
        $response = [
            'podcasts' => $data['shows']['items'] ?? [],
            'total' => $data['shows']['total'] ?? 0,
            'limit' => $data['shows']['limit'] ?? $params['limit'],
            'offset' => $data['shows']['offset'] ?? $params['offset'],
        ];

        return json_encode($response);
    }
    public function getTrendingPodcasts(array $options = []): string
    {
        // Search for podcasts with high popularity indicators
        $queries = [
            'year:2024 popularity:high',
            'genre:comedy',
            'genre:news',
            'genre:true-crime',
        ];

        $allPodcasts = [];
        $query = $options['genre'] ? "genre:{$options['genre']}" : $queries[0];
        
        $params = [
            'q' => $query,
            'type' => 'show',
            'market' => $options['country'] ?? 'US',
            'limit' => $options['limit'] ?? 20,
            'offset' => $options['offset'] ?? 0,
        ];

        $result = $this->makeRequest('/search', $params);
        $data = json_decode($result, true);
        
        $response = [
            'podcasts' => $data['shows']['items'] ?? [],
            'total' => $data['shows']['total'] ?? 0,
            'limit' => $data['shows']['limit'] ?? $params['limit'],
            'offset' => $data['shows']['offset'] ?? $params['offset'],
        ];

        return json_encode($response);
    }

     public function getPodcastRecommendations(array $seedGenres, array $options = []): string
    {
        // Since Spotify doesn't have direct podcast recommendations,
        // we'll search by genres and return popular results
        $genre = implode(' OR genre:', $seedGenres);
        $query = "genre:{$genre}";
        
        return $this->searchPodcasts($query, $options);
    }

    public function searchPodcasts(string $query, array $options = []): string
    {
        $params = [
            'q' => $query,
            'type' => 'show',
            'market' => $options['market'] ?? 'US',
            'limit' => $options['limit'] ?? 20,
            'offset' => $options['offset'] ?? 0,
        ];

        if ($options['include_external'] ?? false) {
            $params['include_external'] = 'audio';
        }

        $result = $this->makeRequest('/search', $params);
        $data = json_decode($result, true);
        
        $response = [
            'podcasts' => $data['shows']['items'] ?? [],
            'total' => $data['shows']['total'] ?? 0,
            'limit' => $data['shows']['limit'] ?? $params['limit'],
            'offset' => $data['shows']['offset'] ?? $params['offset'],
        ];

        return json_encode($response);
    }


   

    private function makeRequest(string $endpoint, array $params = []): string
    {
        try {
            $accessToken = $this->getAccessToken();
            
            $response = Http::withoutVerifying()->withToken($accessToken)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->failed()) {
                throw new Exception('Spotify API request failed: ' . $response->body());
            }

            return $response->body();
        } catch (Exception $e) {
            Log::error('Spotify API request error: ' . $e->getMessage());
            throw $e;
        }
    }
}
