<?php

namespace App\Console\Commands;

use App\Services\TaddyService;
use Illuminate\Console\Command;

class SyncPodcasts extends Command
{
    protected $signature = 'podcasts:sync';
    protected $description = 'Sync podcasts and episodes from Taddy API';

    protected $taddyService;

    public function __construct(TaddyService $taddyService)
    {
        parent::__construct();
        $this->taddyService = $taddyService;
    }

    public function handle()
    {
        $this->info('Starting podcast sync...');
        
        try {
            $this->taddyService->syncPodcasts();
            $this->info('Podcasts synchronized successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to sync podcasts: ' . $e->getMessage());
        }
    }
} 