<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\PodcastIndexCheckNewEpisodes;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\SyncPodcasts::class,
        Commands\ProcessWithdrawals::class,
        PodcastIndexCheckNewEpisodes::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Sync podcasts every hour
        $schedule->command('podcasts:sync')->hourly();
        
        // Process withdrawals every 6 hours
        $schedule->command('withdrawals:process')->everySixHours();

         // Check for new podcast episodes every hour
        $schedule->command('podcasts:check-new-episodes')->hourly();

        $schedule->command(PodcastIndexCheckNewEpisodes::class)->everyFifteenMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 