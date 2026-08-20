<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Le nom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password_confirmation.required' => 'La confirmation du mot de passe est requise.',
            'password_confirmation.same' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'client',
            'is_active' => true,
        ]);

        $token = $user->createToken('Next.js API Token', ['*'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'L\'identifiant est requis.',
            'email.string' => 'L\'identifiant doit être une chaîne de caractères.',
            'password.required' => 'Le mot de passe est requis.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
        ]);

        $loginIdentifier = $request->email;
        $user = User::where('email', $loginIdentifier)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Ce compte est désactivé.'],
            ]);
        }

        $token = $user->createToken('Next.js API Token', ['*'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function googleAuth(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => 'required|string',
        ], [
            'id_token.required' => 'Le token Google est requis.',
        ]);

        // Pour l'instant, on simule une réponse Google
        // En production, il faudrait vérifier le token avec Google
        // Pour l'instant, on retourne une erreur car cette fonctionnalité n'est pas complète
        return response()->json([
            'message' => 'Authentification Google non configurée côté backend.',
        ], 501);
    }
}
