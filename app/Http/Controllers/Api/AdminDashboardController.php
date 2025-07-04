<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListeningHistory;
use App\Models\BlockedIp;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $playedPodcasts = ListeningHistory::count();
        $totalEarnings = ListeningHistory::where('is_eligible_for_earnings', true)->sum('earnings');
        $blockedIps = BlockedIp::count();
        $totalUsers = User::count();
        $usersByCountry = User::select('country', DB::raw('count(*) as total'))->groupBy('country')->get();
        $totalPayout = Withdrawal::where('status', 'completed')->sum('amount');
        $payoutDetails = Withdrawal::orderBy('created_at', 'desc')->take(20)->get();
        $failedPayouts = Withdrawal::where('status', 'failed')->count();

        return response()->json([
            'played_podcasts' => $playedPodcasts,
            'total_earnings' => $totalEarnings,
            'blocked_ips' => $blockedIps,
            'total_users' => $totalUsers,
            'users_by_country' => $usersByCountry,
            'total_payout' => $totalPayout,
            'payout_details' => $payoutDetails,
            'failed_payouts' => $failedPayouts,
        ]);
    }

    public function payouts()
    {
        $payouts = Withdrawal::orderBy('created_at', 'desc')->paginate(20);
        return response()->json($payouts);
    }

    public function blockedIps()
    {
        $ips = BlockedIp::all();
        return response()->json($ips);
    }

    public function usersByCountry()
    {
        $users = User::select('country', DB::raw('count(*) as total'))->groupBy('country')->get();
        return response()->json($users);
    }
} 