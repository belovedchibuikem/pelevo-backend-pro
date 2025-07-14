<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    // GET /api/profile
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'profileImage' => $user->profile_image_url ?? null,
                'balance' => $user->balance ?? 0.0,
                'subscribedCategories' => $user->subscribed_categories ?? [],
                'memberSince' => $user->created_at ? $user->created_at->format('F Y') : null,
                // Add more fields as needed
            ]
        ]);
    }

    // PATCH /api/profile
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->fill($request->validated());
        if ($request->hasFile('profileImage')) {
            $file = $request->file('profileImage');
            $path = $file->store('profile_images', 'public');
            $user->profile_image_url = Storage::url($path);
        }
        $user->save();
        return response()->json(['success' => true, 'message' => 'Profile updated successfully.', 'data' => $user]);
    }

    // DELETE /api/profile
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true, 'message' => 'Account deleted.']);
    }

    // POST /api/profile/fcm-token
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);
        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();
        return response()->json(['success' => true, 'message' => 'FCM token updated.']);
    }
} 