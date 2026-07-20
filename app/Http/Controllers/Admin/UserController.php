<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->filled('search'), function ($q) use ($request) {
            $term = '%'.$request->search.'%';
            $q->where('name', 'like', $term)
                ->orWhere('email', 'like', $term)
                ->orWhere('phone', 'like', $term);
        })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'role' => ['required', Rule::in(['admin', 'agent', 'livreur', 'cuisinier', 'client'])],
            'password' => ['required', 'min:8'],
            'is_active' => ['boolean'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé.');
    }

    public function show(User $user)
    {
        $user->load([
            'ordersAsAgent' => fn ($q) => $q->latest()->limit(10),
            'ordersAsClient' => fn ($q) => $q->latest()->limit(10),
            'subscriptions' => fn ($q) => $q->latest()->limit(10),
            'agentSubscriptions' => fn ($q) => $q->latest()->limit(10),
            'deliveries' => fn ($q) => $q->latest()->limit(10),
            'withdrawals' => fn ($q) => $q->latest()->limit(10),
        ]);

        $stats = match ($user->role) {
            'agent' => [
                'orders_count' => $user->ordersAsAgent()->count(),
                'delivered_count' => $user->ordersAsAgent()->where('status', 'delivered')->count(),
                'subscriptions_count' => $user->agentSubscriptions()->count(),
                'withdrawals_count' => $user->withdrawals()->count(),
            ],
            'livreur' => [
                'deliveries_count' => $user->deliveries()->count(),
                'delivered_count' => $user->deliveries()->where('status', 'delivered')->count(),
            ],
            'client' => [
                'orders_count' => $user->ordersAsClient()->count(),
                'subscriptions_count' => $user->subscriptions()->count(),
            ],
            default => [],
        };

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'role' => ['required', Rule::in(['admin', 'agent', 'livreur', 'cuisinier', 'client'])],
            'is_active' => ['boolean'],
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }
}
