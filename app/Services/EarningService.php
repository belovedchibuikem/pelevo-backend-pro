<?php

namespace App\Services;

use App\Models\User;
use App\Models\Episode;
use App\Models\ListeningHistory;
use App\Models\BlockedIp;
use Illuminate\Support\Facades\DB;

class EarningService
{
    public function calculateEarnings(User $user, Episode $episode, int $durationListened, string $ipAddress)
    {
        // Check if IP is blocked
        if (BlockedIp::where('ip_address', $ipAddress)->exists()) {
            return [
                'success' => false,
                'message' => 'IP address is blocked from earning',
                'earnings' => 0,
            ];
        }

        // Check if episode's podcast is monetized
        if (!$episode->podcast->is_monetized) {
            return [
                'success' => false,
                'message' => 'This podcast is not monetized',
                'earnings' => 0,
            ];
        }

        // Calculate earnings based on duration listened and podcast's earning rate
        $earnings = ($durationListened / 60) * $episode->podcast->earning_rate;

        // Record listening history
        $listeningHistory = ListeningHistory::create([
            'user_id' => $user->id,
            'episode_id' => $episode->id,
            'duration_listened' => $durationListened,
            'earnings' => $earnings,
            'ip_address' => $ipAddress,
            'is_eligible_for_earnings' => true,
        ]);

        // Update user's wallet
        DB::transaction(function () use ($user, $earnings) {
            $user->wallet()->update([
                'balance' => DB::raw("balance + {$earnings}")
            ]);
        });

        return [
            'success' => true,
            'message' => 'Earnings calculated successfully',
            'earnings' => $earnings,
        ];
    }

    public function getTotalEarnings(User $user)
    {
        return ListeningHistory::where('user_id', $user->id)
            ->where('is_eligible_for_earnings', true)
            ->sum('earnings');
    }

    public function getEarningsByDate(User $user, $startDate, $endDate)
    {
        return ListeningHistory::where('user_id', $user->id)
            ->where('is_eligible_for_earnings', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('earnings');
    }

    public function getEarningsByPodcast(User $user, $podcastId)
    {
        return ListeningHistory::where('user_id', $user->id)
            ->where('is_eligible_for_earnings', true)
            ->whereHas('episode', function ($query) use ($podcastId) {
                $query->where('podcast_id', $podcastId);
            })
            ->sum('earnings');
    }
} 