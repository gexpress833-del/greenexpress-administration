<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'category' => ['required', 'in:information,alert,success,order,delivery,subscription'],
            'target' => ['required', 'in:all,admin,agent,livreur,cuisinier,client'],
        ]);

        $target = $data['target'];

        if ($target === 'all') {
            NotificationService::notifyAllUsers(
                $data['title'],
                $data['message'],
                'admin_broadcast',
                null,
                null,
                null,
                $data['category'],
            );
        } else {
            $users = User::where('role', $target)->where('is_active', true)->get();
            $notificationService = app(NotificationService::class);
            foreach ($users as $user) {
                $notificationService->notify(
                    $user,
                    $data['category'],
                    $data['title'],
                    $data['message'],
                    'admin_broadcast',
                );
            }
        }

        return redirect()->route('admin.notifications.create')
            ->with('success', 'Notification envoyée avec succès.');
    }
}
