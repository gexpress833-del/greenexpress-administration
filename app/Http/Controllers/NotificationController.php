<?php

namespace App\Http\Controllers;

use App\Models\Notification as AppNotification;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $appNotifications = $request->user()
            ->appNotifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(fn (AppNotification $n) => $this->mapAppNotification($n));

        // Legacy Laravel database notifications (pre-unification)
        $laravelNotifications = $request->user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'read_at' => $n->read_at,
                'created_at' => $n->created_at,
                'data' => [
                    'title' => data_get($n->data, 'title', 'Notification'),
                    'message' => data_get($n->data, 'message', data_get($n->data, 'body', '')),
                    'type' => data_get($n->data, 'type', 'custom'),
                    'category' => data_get($n->data, 'category', 'information'),
                    'color' => data_get($n->data, 'color', $this->colorForCategory(data_get($n->data, 'category', 'information'))),
                    'icon' => data_get($n->data, 'icon', $this->iconForCategory(data_get($n->data, 'category', 'information'))),
                    'url' => data_get($n->data, 'url'),
                ],
                'detail_url' => route('notifications.show', ['id' => $n->id, 'source' => 'laravel']),
                'source' => 'laravel',
            ]);

        $notifications = $appNotifications->merge($laravelNotifications)
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        return response()->json($notifications);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $laravelCount = $request->user()->unreadNotifications()->count();
        $appCount = $request->user()->unreadAppNotifications()->count();

        return response()->json([
            'count' => $laravelCount + $appCount,
        ]);
    }

    public function history(Request $request): View
    {
        $appNotifications = $request->user()
            ->appNotifications()
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->latest()
            ->paginate(25);

        return view('notifications.history', ['notifications' => $appNotifications]);
    }

    public function show(Request $request, string $id): View
    {
        $source = $request->string('source')->toString() ?: 'app';

        if ($source === 'laravel') {
            $notification = $request->user()->notifications()->findOrFail($id);
            $data = $notification->data;
            $notification->markAsRead();

            $entity = $this->resolveEntity(data_get($data, 'url'), data_get($data, 'message'));

            return view('notifications.show', [
                'title' => data_get($data, 'title', 'Notification'),
                'message' => data_get($data, 'message', data_get($data, 'body', '')),
                'category' => 'Information',
                'icon' => '🔔',
                'typeColor' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                'createdAt' => $notification->created_at,
                'url' => data_get($data, 'url'),
                'whatsappLink' => data_get($data, 'whatsapp_link'),
                'entity' => $entity,
            ]);
        }

        $notification = $request->user()->appNotifications()->with('notifiable')->findOrFail($id);
        $notification->markAsRead();

        $entity = $notification->notifiable ?? $this->resolveEntity($notification->url, $notification->message);

        return view('notifications.show', [
            'title' => $notification->title,
            'message' => $notification->message,
            'category' => $notification->category_label,
            'icon' => $notification->icon,
            'typeColor' => $notification->type_color,
            'createdAt' => $notification->created_at,
            'url' => $notification->url,
            'whatsappLink' => $notification->whatsapp_link,
            'entity' => $entity,
        ]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $source = $request->input('source', 'app');

        if ($source === 'app') {
            $notification = $request->user()->appNotifications()->findOrFail($id);
            $notification->markAsRead();
        } else {
            $notification = $request->user()->notifications()->findOrFail($id);
            $notification->markAsRead();
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        $request->user()->appNotifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * @return array<string, mixed>
     */
    private function mapAppNotification(AppNotification $n): array
    {
        $category = $n->category ?: 'information';

        return [
            'id' => $n->id,
            'read_at' => $n->is_read ? ($n->read_at?->toDateTimeString() ?? now()->toDateTimeString()) : null,
            'created_at' => $n->created_at,
            'data' => [
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'category' => $category,
                'color' => $this->colorForCategory($category),
                'icon' => $this->iconForCategory($category),
                'url' => $n->url,
                'whatsapp_link' => $n->whatsapp_link,
            ],
            'detail_url' => route('notifications.show', ['id' => $n->id, 'source' => 'app']),
            'source' => 'app',
        ];
    }

    private function colorForCategory(string $category): string
    {
        return match ($category) {
            'reward', 'badge', 'bonus' => 'amber',
            'delivery' => 'blue',
            'subscription' => 'green',
            'order' => 'purple',
            'success' => 'green',
            'alert' => 'red',
            default => 'gray',
        };
    }

    private function iconForCategory(string $category): string
    {
        return match ($category) {
            'reward', 'bonus' => 'check-circle',
            'badge' => 'user-check',
            'delivery' => 'truck',
            'subscription' => 'clipboard-list',
            'order' => 'clipboard-list',
            'success' => 'check-circle',
            'alert' => 'x-circle',
            default => 'check-circle',
        };
    }

    private function resolveEntity(?string $url, ?string $text): ?object
    {
        if (empty($text)) {
            return null;
        }

        if (preg_match('/\b([A-Z0-9]{2,}-[A-Z0-9]{6,})\b/', $text, $matches)) {
            $order = Order::with(['items.meal', 'agent', 'delivery.livreur'])->where('code', $matches[1])->first();
            if ($order) {
                return $order;
            }
        }

        if (preg_match('/\b([A-Z]{2}[0-9A-Z]+)\b/', $text, $matches)) {
            $order = Order::with(['items.meal', 'agent', 'delivery.livreur'])->where('code', $matches[1])->first();
            if ($order) {
                return $order;
            }
        }

        return null;
    }
}
