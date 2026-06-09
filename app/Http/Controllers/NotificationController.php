<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Laravel built-in notifications
        $laravelNotifications = $request->user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'read_at' => $n->read_at,
                'created_at' => $n->created_at,
                'data' => $n->data,
                'source' => 'laravel',
            ]);

        // App notifications
        $appNotifications = $request->user()
            ->appNotifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'read_at' => $n->is_read ? now()->toDateTimeString() : null,
                'created_at' => $n->created_at,
                'data' => [
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'color' => match($n->type) {
                        'meal' => 'orange',
                        'subscription_type' => 'blue',
                        'exchange_rate' => 'green',
                        'order' => 'purple',
                        'delivery' => 'amber',
                        default => 'gray',
                    },
                    'icon' => match($n->type) {
                        'meal' => 'clipboard-list',
                        'subscription_type' => 'check-circle',
                        'exchange_rate' => 'banknote',
                        'order' => 'clipboard-list',
                        'delivery' => 'truck',
                        default => 'check-circle',
                    },
                    'url' => null,
                ],
                'source' => 'app',
            ]);

        $notifications = $laravelNotifications->merge($appNotifications)
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        return response()->json($notifications);
    }

    public function unreadCount(Request $request)
    {
        $laravelCount = $request->user()->unreadNotifications()->count();
        $appCount = $request->user()->unreadAppNotifications()->count();

        return response()->json([
            'count' => $laravelCount + $appCount,
        ]);
    }

    public function history(Request $request)
    {
        $appNotifications = $request->user()
            ->appNotifications()
            ->latest()
            ->paginate(25);

        return view('notifications.history', ['notifications' => $appNotifications]);
    }

    public function markAsRead(Request $request, string $id)
    {
        if ($request->input('source') === 'app') {
            $notification = $request->user()->appNotifications()->findOrFail($id);
            $notification->markAsRead();
        } else {
            $notification = $request->user()->notifications()->findOrFail($id);
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        // Mark Laravel notifications as read
        $request->user()->unreadNotifications->markAsRead();

        // Mark app notifications as read
        $request->user()->appNotifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
