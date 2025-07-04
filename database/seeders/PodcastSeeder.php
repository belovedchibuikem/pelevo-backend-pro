<?php
// database/seeders/PodcastSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PodcastSubscription;
use App\Models\PodcastNotification;

class PodcastSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample users if they don't exist
        $users = User::factory(5)->create();

        // Sample show IDs (replace with real Spotify show IDs)
        $sampleShows = [
            [
                'show_id' => '4rOoJ6Egrf8K2IrywzwOMk',
                'show_name' => 'The Joe Rogan Experience',
                'show_description' => 'The official podcast of comedian Joe Rogan.',
                'show_image_url' => 'https://example.com/image1.jpg'
            ],
            [
                'show_id' => '2MAi0BvDc6GTFvKFPXnkCL',
                'show_name' => 'Serial',
                'show_description' => 'Serial is a podcast from the creators of This American Life.',
                'show_image_url' => 'https://example.com/image2.jpg'
            ],
            [
                'show_id' => '3SwNVcIanhDmOM2wlTJqOv',
                'show_name' => 'Crime Junkie',
                'show_description' => 'If you can never get enough true crime...',
                'show_image_url' => 'https://example.com/image3.jpg'
            ]
        ];

        // Create subscriptions for users
        foreach ($users as $user) {
            foreach ($sampleShows as $show) {
                PodcastSubscription::create([
                    'user_id' => $user->id,
                    'show_id' => $show['show_id'],
                    'show_name' => $show['show_name'],
                    'show_description' => $show['show_description'],
                    'show_image_url' => $show['show_image_url'],
                    'subscribed_at' => now()->subDays(rand(1, 30)),
                    'is_active' => true,
                    'notify_new_episodes' => rand(0, 1) == 1
                ]);
            }

            // Create sample notifications
            PodcastNotification::create([
                'user_id' => $user->id,
                'type' => 'new_episode',
                'title' => 'New Episode Available',
                'message' => 'New episode from The Joe Rogan Experience',
                'data' => [
                    'episode' => [
                        'id' => 'sample_episode_id',
                        'name' => 'Sample Episode',
                        'description' => 'This is a sample episode'
                    ],
                    'show_id' => '4rOoJ6Egrf8K2IrywzwOMk'
                ],
                'show_id' => '4rOoJ6Egrf8K2IrywzwOMk',
                'episode_id' => 'sample_episode_id',
                'is_read' => false
            ]);
        }
    }
}
