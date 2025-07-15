<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PodcastIndexService
{
    private $apiKey;
    private $apiSecret;
    private $baseUrl = 'https://api.podcastindex.org/api/1.0';

    public function __construct()
    {
        $this->apiKey = config('services.podcastindex.key');
        $this->apiSecret = config('services.podcastindex.secret');
    }

    /**
     * Generate authentication headers for API requests
     */
    private function getAuthHeaders(): array
    {
        $apiHeaderTime = time();
        $data4Hash = $this->apiKey . $this->apiSecret . $apiHeaderTime;
        $sha1Hash = sha1($data4Hash);
       

        return [
            'X-Auth-Date' => $apiHeaderTime,
            'X-Auth-Key' => $this->apiKey,
            'Authorization' => $sha1Hash,
            'User-Agent' => config('app.name', 'Pelevo') . '/1.3'
        ];
    }

    /**
     * Make API request to PodcastIndex
     */
    private function makeRequest(string $endpoint, array $params = []): array
    {
        $response = Http::withoutVerifying()->withHeaders($this->getAuthHeaders())
            ->get($this->baseUrl . $endpoint, $params);

        if ($response->failed()) {
            throw new \Exception('PodcastIndex API request failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get all podcast categories
     */
    public function getCategories(): array
    {
        return Cache::remember('podcast_categories', 3600, function () {
            $categories = $this->makeRequest('/categories/list');
            
            // Get category counts for each category
            if (isset($categories['categories']) && is_array($categories['categories'])) {
                $categoriesWithCounts = [];
                foreach ($categories['categories'] as $id => $name) {
                    
                    //$count = $this->searchPodcasts($name['name']);
                    //dd($count);
                    $categoriesWithCounts[] = [
                        'id' => (string)$id,
                        'name' => $name,
                        'count' => 0,
                    ];
                }
                return $categoriesWithCounts;
            }
            
            return $categories;
        });
    }

    /**
     * Get count of podcasts in a specific category
     */
    public function getCategoryCount(int $categoryId): int
    {
        $cacheKey = "category_count_{$categoryId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($categoryId) {
            try {
                // Get a sample of podcasts from the category to estimate count
                $podcasts = $this->makeRequest('/podcasts/bycat', [
                    'cat' => $categoryId,
                    'max' => 1, // Just get 1 to check if category exists
                    'clean' => true,
                    'lang'  =>'en'
                ]);
                
                if (isset($podcasts['feeds']) && is_array($podcasts['feeds'])) {
                    // If we get results, try to get a larger sample to estimate count
                    $samplePodcasts = $this->makeRequest('/podcasts/bycat', [
                        'cat' => $categoryId,
                        'max' => 1000, // Get up to 100 to estimate
                        'clean' => true
                    ]);
                    
                    if (isset($samplePodcasts['feeds'])) {
                        $count = count($samplePodcasts['feeds']);
                        
                        // If we got 100 results, the actual count is likely much higher
                        // We'll use this as a minimum estimate
                        if ($count >= 100) {
                            return $count + rand(50, 500); // Add some randomness for estimate
                        }
                        
                        return $count;
                    }
                }
                
                return 0;
            } catch (\Exception $e) {
                // Log error but don't fail the entire request
                Log::warning("Failed to get count for category {$categoryId}: " . $e->getMessage());
                return 0;
            }
        });
    }

    /**
     * Search podcasts by term
     */
    public function searchPodcasts(string $term, int $max = 1000): array
    {
        return $this->makeRequest('/search/byterm', [
            'q' => $term,
            'lang'  =>'en',
            'max' => $max,
            'clean' => true
        ]);
    }

    /**
     * Get podcast by feed ID
     */
    public function getPodcastById(int $feedId): array
    {
        return $this->makeRequest('/podcasts/byfeedid', [
            'id' => $feedId
        ]);
    }

    /**
     * Get podcast by feed URL
     */
    public function getPodcastByUrl(string $feedUrl): array
    {
        return $this->makeRequest('/podcasts/byfeedurl', [
            'url' => $feedUrl
        ]);
    }

    /**
     * Get episodes by feed ID
     */
    public function getEpisodesByFeedId(int $feedId, int $max = 1000, int $since = 0): array
    {
        $params = [
            'id' => $feedId,
            'max' => $max,
            'lang'  =>'en'
        ];

        if ($since) {
            $params['since'] = $since;
        }

        return $this->makeRequest('/episodes/byfeedid', $params);
    }

    /**
     * Get recent episodes across all podcasts
     */
    public function getRecentEpisodes(int $max = 1000, array $excludeString = [], array $before = []): array
    {
        $params = [
            'max' => $max,
            'clean' => true,
            'lang'  =>'en'
        ];

        if (!empty($excludeString)) {
            $params['excludeString'] = implode(',', $excludeString);
        }

        return $this->makeRequest('/recent/episodes', $params);
    }

    /**
     * Get trending podcasts
     */
    public function getTrendingPodcasts(int $max = 1000, string $since = '', string $cat = ''): array
    {
        $params = [
            'max' => $max,
            'clean' => true,
            'lang'  =>'en',
        ];

        if ($since) {
            $params['since'] = $since;
        }

        if ($cat) {
            $params['cat'] = $cat;
        }

        $cacheKey = 'trending_podcasts' .$max;
        try {
            return Cache::remember($cacheKey, 1800, function () use ($params) {
                return $this->makeRequest('/podcasts/trending', $params);
            });
        } catch (\Exception $e) {
            if (method_exists($e, 'getCode') && $e->getCode() == 1032) {
                // Log and treat as cache miss
                Log::warning("Cache record not found (1032) for key: $cacheKey. Refetching and caching.");
                $data = $this->makeRequest('/podcasts/trending', $params);
                Cache::put($cacheKey, $data, 1800);
                return $data;
            }
            throw $e;
        }
    }

    /**
     * Get new podcasts
     */
    public function getNewPodcasts(int $max = 1000, string $since = ''): array
    {
        $params = [
            'max' => $max,
            'clean' => true,
            'lang'  =>'en'
        ];

        if ($since) {
            $params['since'] = $since;
        }

        $cacheKey = 'new_podcasts' . md5(serialize($params));
        try {
            return Cache::remember($cacheKey, 1800, function () use ($params) {
                return $this->makeRequest('/recent/feeds', $params);
            });
        } catch (\Exception $e) {
            if (method_exists($e, 'getCode') && $e->getCode() == 1032) {
                // Log and treat as cache miss
                Log::warning("Cache record not found (1032) for key: $cacheKey. Refetching and caching.");
                $data = $this->makeRequest('/recent/feeds', $params);
                Cache::put($cacheKey, $data, 1800);
                return $data;
            }
            throw $e;
        }
    }

    /**
     * Get popular podcasts
     */
    public function getPopularPodcasts(int $max = 1000): array
    {
        $cacheKey = 'popular_podcasts';
        try {
            return Cache::remember($cacheKey, 3600, function () use ($max) {
                return $this->makeRequest('/recent/feeds', [
                    'max' => $max,
                    'clean' => true,
                    'lang'  =>'en'
                ]);
            });
        } catch (\Exception $e) {
            if (method_exists($e, 'getCode') && $e->getCode() == 1032) {
                // Log and treat as cache miss
                Log::warning("Cache record not found (1032) for key: $cacheKey. Refetching and caching.");
                $data = $this->makeRequest('/recent/feeds', [
                    'max' => $max,
                    'clean' => true,
                    'lang'  =>'en'
                ]);
                Cache::put($cacheKey, $data, 3600);
                return $data;
            }
            throw $e;
        }
    }

    /**
     * Get podcast stats
     */
    public function getPodcastStats(): array
    {
        $cacheKey = 'podcast_stats';
        try {
            return Cache::remember($cacheKey, 3600, function () {
                return $this->makeRequest('/stats/current');
            });
        } catch (\Exception $e) {
            if (method_exists($e, 'getCode') && $e->getCode() == 1032) {
                // Log and treat as cache miss
                Log::warning("Cache record not found (1032) for key: $cacheKey. Refetching and caching.");
                $data = $this->makeRequest('/stats/current');
                Cache::put($cacheKey, $data, 3600);
                return $data;
            }
            throw $e;
        }
    }

    /**
     * Get podcasts by category
     */
    public function getPodcastsByCategory(string $category, int $max = 1000, string $term): array
    {
        return $this->makeRequest('/search/byterm', [
            'q'=>$term,
            'cat' => $category,
            'max' => $max,
            'lang'=>'en',
            'clean' => true,
            
        ]);
    }

    /**
     * Get episode by GUID
     */
    public function getEpisodeByGuid(string $guid, int $feedId = null): array
    {
        $params = ['guid' => $guid];
        
        if ($feedId) {
            $params['feedid'] = $feedId;
        }

        return $this->makeRequest('/episodes/byguid', $params);
    }

    /**
     * Get random podcasts
     */
    public function getRandomPodcasts(int $max = 1000, string $lang = '', string $cat = ''): array
    {
        $params = [
            'max' => $max,
            'clean' => true
        ];

        if ($lang) {
            $params['lang'] = $lang;
        }

        if ($cat) {
            $params['cat'] = $cat;
        }

        return $this->makeRequest('/podcasts/random', $params);
    }

    /**
     * Get podcasts for the True Crime category
     */
    public function getTrueCrimePodcasts(int $max = 1000): array
    {
        $cacheKey = 'true_crime_podcasts_' . $max;
        try {
            return Cache::remember($cacheKey, 1800, function () use ($max) {
                return $this->getPodcastsByCategory('103', $max, 'True Crime');
            });
        } catch (\Exception $e) {
            if (method_exists($e, 'getCode') && $e->getCode() == 1032) {
                // Log and treat as cache miss
                Log::warning("Cache record not found (1032) for key: $cacheKey. Refetching and caching.");
                $data = $this->getPodcastsByCategory('103', $max, 'True Crime');
                Cache::put($cacheKey, $data, 1800);
                return $data;
            }
            throw $e;
        }
    }

    public function getSearchPodcastByCategory(int $max = 1000, string $catId='', string $catName=''): array
    {
        $cacheKey = 'search_podcast_by_category_' . $max.$catId.$catName;
        try {
            return Cache::remember($cacheKey, 1800, function () use ($max,$catId,$catName) {
                return $this->getPodcastsByCategory($catId, $max, $catName);
            });
        } catch (\Exception $e) {
            if (method_exists($e, 'getCode') && $e->getCode() == 1032) {
                // Log and treat as cache miss
                Log::warning("Cache record not found (1032) for key: $cacheKey. Refetching and caching.");
                $data = $this->getPodcastsByCategory($catId, $max, $catName);
                Cache::put($cacheKey, $data, 1800);
                return $data;
            }
            throw $e;
        }
    }

    /**
     * Get podcasts for the Health category
     */
    public function getHealthPodcasts(int $max = 1000): array
    {
        
        $cacheKey = 'health_podss';
        try {
            return Cache::remember($cacheKey, 1800, function () use ($max) {
              
                return $this->getPodcastsByCategory('29', $max, 'Health');
            });
        } catch (\Exception $e) {
            if (method_exists($e, 'getCode') && $e->getCode() == 1032) {
                // Log and treat as cache miss
                Log::warning("Cache record not found (1032) for key: $cacheKey. Refetching and caching.");
                $data = $this->getPodcastsByCategory('29', $max, 'Health');
                Cache::put($cacheKey, $data, 1800);
                return $data;
            }
            throw $e;
        }
    }

    /**
     * Fetch podcast details by PodcastIndex feed ID (static helper for controller use)
     */
    public static function fetchPodcast($podcastindex_podcast_id)
    {
        $service = new self();
        $result = $service->getPodcastById((int)$podcastindex_podcast_id);
        if (isset($result['feed'])) {
            return $result['feed'];
        }
        throw new \Exception('Podcast not found in PodcastIndex');
    }
}