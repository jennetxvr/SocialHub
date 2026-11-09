<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $notifications = $currentUser->notifications()->paginate(20);

        // Mark as read when viewing page
        $currentUser->unreadNotifications->markAsRead();

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    public function unreadCount(): JsonResponse
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        $count = $currentUser?->unreadNotifications()->count() ?? 0;

        return response()->json(['unread_count' => $count]);
    }
}
