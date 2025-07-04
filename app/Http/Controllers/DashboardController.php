<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use App\Models\Notification;
use App\Models\Podcast;
use App\Models\Subscription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalEarnings = Earning::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $pendingWithdrawals = $user->withdrawals()
            ->where('status', 'pending')
            ->sum('amount');

        $totalSubscribers = Subscription::where('podcast_id', function ($query) use ($user) {
            $query->select('id')
                ->from('podcasts')
                ->where('user_id', $user->id);
        })->count();

        $totalEpisodes = Podcast::where('user_id', $user->id)
            ->withCount('episodes')
            ->get()
            ->sum('episodes_count');

        $recentEarnings = Earning::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        $recentNotifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalEarnings',
            'pendingWithdrawals',
            'totalSubscribers',
            'totalEpisodes',
            'recentEarnings',
            'recentNotifications'
        ));
    }
} 