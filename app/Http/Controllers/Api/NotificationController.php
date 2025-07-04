<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    public function unread(Request $request)
    {
        $notifications = $this->notificationService->getUnreadNotifications($request->user());
        return response()->json($notifications);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        $this->notificationService->markAsRead($notification);
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        $this->notificationService->markAllAsRead($request->user());
        return response()->json(['message' => 'All notifications marked as read']);
    }
} 