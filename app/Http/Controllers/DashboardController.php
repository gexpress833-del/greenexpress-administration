<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => redirect()->route('agent.dashboard'),
            'livreur' => redirect()->route('livreur.dashboard'),
            'cuisinier' => redirect()->route('cuisinier.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
