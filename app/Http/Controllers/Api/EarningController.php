<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Episode;
use App\Models\ListeningHistory;
use App\Models\User;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    /**
     * Record a user's listening activity.
     *
     * @param Request $request
     * @param Episode $episode
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordListening(Request $request, Episode $episode)
    {
        $user = $request->user();

        // Record listening history
        ListeningHistory::create([
            'user_id' => $user->id,
            'episode_id' => $episode->id,
            'listened_at' => now(),
        ]);

        // If the podcast is monetized, record earnings
        if ($episode->podcast && $episode->podcast->is_monetized) {
            $earningRate = $episode->podcast->earning_rate; // Assuming earning rate is per listen or view
            Earning::create([
                'user_id' => $user->id,
                'podcast_id' => $episode->podcast->id,
                'episode_id' => $episode->id,
                'amount' => $earningRate,
                'source' => 'listen',
                'status' => 'completed',
            ]);
        }

        return response()->json(['message' => 'Listening activity recorded.'], 200);
    }

    /**
     * Get total earnings for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTotalEarnings(Request $request)
    {
        $user = $request->user();
        $totalEarnings = Earning::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json(['total_earnings' => $totalEarnings]);
    }

    /**
     * Get earnings grouped by date for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEarningsByDate(Request $request)
    {
        $user = $request->user();
        $earningsByDate = Earning::where('user_id', $user->id)
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total_amount')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($earningsByDate);
    }

    /**
     * Get earnings by a specific podcast for the authenticated user.
     *
     * @param Request $request
     * @param string $podcastId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEarningsByPodcast(Request $request, string $podcastId)
    {
        $user = $request->user();
        $earnings = Earning::where('user_id', $user->id)
            ->where('podcast_id', $podcastId)
            ->where('status', 'completed')
            ->get();

        return response()->json($earnings);
    }

    /**
     * Get listening history for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListeningHistory(Request $request)
    {
        $user = $request->user();
        $history = ListeningHistory::where('user_id', $user->id)
            ->with('episode.podcast')
            ->latest('listened_at')
            ->get();

        return response()->json($history);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $earnings = Earning::where('user_id', $user->id)->paginate(10);
        return response()->json($earnings);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        $earning = Earning::where('user_id', $user->id)->findOrFail($id);
        return response()->json($earning);
    }
} 