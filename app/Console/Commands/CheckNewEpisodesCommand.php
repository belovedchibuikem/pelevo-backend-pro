<?php
namespace App\Console\Commands;

use App\Jobs\CheckNewEpisodesJob;
use Illuminate\Console\Command;

class CheckNewEpisodesCommand extends Command
{
    protected $signature = 'podcasts:check-new-episodes';
    protected $description = 'Check for new episodes from subscribed podcasts';

    public function handle(): int
    {
        $this->info('Checking for new episodes...');
        
        CheckNewEpisodesJob::dispatch();
        
        $this->info('New episode check job dispatched successfully.');
        
        return 0;
    }
}