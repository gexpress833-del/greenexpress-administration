<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->take(20)
            ->get();

        return response()->json($notifications);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function history(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(25);

        return view('notifications.history', compact('notifications'));
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->has('redirect') && $notification->data['url'] ?? false) {
            return redirect($notification->data['url']);
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
