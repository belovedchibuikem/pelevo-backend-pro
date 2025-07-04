<?php

namespace App\Console\Commands;

use App\Models\Withdrawal;
use App\Services\FlutterwaveService;
use Illuminate\Console\Command;

class ProcessWithdrawals extends Command
{
    protected $signature = 'withdrawals:process';
    protected $description = 'Process pending withdrawals';

    protected $flutterwaveService;

    public function __construct(FlutterwaveService $flutterwaveService)
    {
        parent::__construct();
        $this->flutterwaveService = $flutterwaveService;
    }

    public function handle()
    {
        $this->info('Starting withdrawal processing...');
        
        $pendingWithdrawals = Withdrawal::where('status', 'pending')
            ->where('created_at', '<=', now()->subHours(24))
            ->get();

        foreach ($pendingWithdrawals as $withdrawal) {
            try {
                $this->flutterwaveService->processWithdrawal($withdrawal);
                $this->info("Processed withdrawal #{$withdrawal->id} successfully");
            } catch (\Exception $e) {
                $this->error("Failed to process withdrawal #{$withdrawal->id}: " . $e->getMessage());
            }
        }

        $this->info('Withdrawal processing completed!');
    }
} 