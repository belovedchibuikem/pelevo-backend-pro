<?php
// app/Http/Middleware/SpotifyTokenMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpotifyTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user || !$user->spotify_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'Spotify authentication required'
            ], 401);
        }

        return $next($request);
    }
}
