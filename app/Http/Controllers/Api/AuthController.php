<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

        $idToken = $request->id_token;

        try {
            // Récupérer les clés publiques de Google
            $googleKeysUrl = 'https://www.googleapis.com/oauth2/v3/certs';
            $keysResponse = Http::get($googleKeysUrl);

            if (! $keysResponse->successful()) {
                return response()->json([
                    'message' => 'Impossible de récupérer les clés publiques Google.',
                ], 500);
            }

            $keys = $keysResponse->json('keys');

            // Décoder le token pour obtenir l'header et trouver la clé correspondante
            $tokenParts = explode('.', $idToken);
            if (count($tokenParts) !== 3) {
                return response()->json([
                    'message' => 'Format de token invalide.',
                ], 400);
            }

            $header = json_decode(base64_decode($tokenParts[0]), true);
            $kid = $header['kid'] ?? null;

            if (! $kid) {
                return response()->json([
                    'message' => 'Token sans identifiant de clé.',
                ], 400);
            }

            // Trouver la clé correspondante
            $matchingKey = collect($keys)->first(fn ($key) => ($key['kid'] ?? null) === $kid);

            if (! $matchingKey) {
                return response()->json([
                    'message' => 'Clé publique non trouvée.',
                ], 400);
            }

            // Convertir la clé PEM
            $pem = $this->convertToPem($matchingKey);

            // Vérifier le token
            $decoded = JWT::decode($idToken, new Key($pem, 'RS256'));

            // Vérifier les claims
            if (($decoded->iss ?? '') !== 'https://accounts.google.com') {
                return response()->json([
                    'message' => 'Émetteur du token invalide.',
                ], 400);
            }

            $clientId = config('services.google.client_id');
            if (! $clientId) {
                return response()->json([
                    'message' => 'Configuration Google manquante.',
                ], 500);
            }

            if (($decoded->aud ?? '') !== $clientId) {
                return response()->json([
                    'message' => 'Audience du token invalide.',
                ], 400);
            }

            if (($decoded->exp ?? 0) < time()) {
                return response()->json([
                    'message' => 'Token expiré.',
                ], 400);
            }

            // Extraire les informations utilisateur
            $email = $decoded->email ?? null;
            $name = $decoded->name ?? null;
            $picture = $decoded->picture ?? null;

            if (! $email) {
                return response()->json([
                    'message' => 'Email non trouvé dans le token.',
                ], 400);
            }

            // Créer ou mettre à jour l'utilisateur
            $user = User::where('email', $email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $name ?? explode('@', $email)[0],
                    'email' => $email,
                    'password' => Hash::make(str()->random(32)), // Mot de passe aléatoire
                    'role' => 'client',
                    'is_active' => true,
                    'phone' => null,
                ]);
            } else {
                // Mettre à jour le nom si nécessaire
                if ($name && $user->name !== $name) {
                    $user->name = $name;
                    $user->save();
                }
            }

            // Générer un token Sanctum
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
                    'picture' => $picture,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la vérification du token: '.$e->getMessage(),
            ], 400);
        }
    }

    /**
     * Convertit une clé Google JWK en format PEM
     */
    private function convertToPem(array $jwk): string
    {
        $modulus = $this->base64UrlDecode($jwk['n']);
        $exponent = $this->base64UrlDecode($jwk['e']);

        $modulus = '00'.bin2hex($modulus);
        $exponent = bin2hex($exponent);

        $components = [
            ['tag' => "\x02", 'length' => strlen($exponent) / 2, 'content' => $exponent],
            ['tag' => "\x02", 'length' => strlen($modulus) / 2, 'content' => $modulus],
        ];

        $publicKey = "-----BEGIN PUBLIC KEY-----\n";

        foreach ($components as $component) {
            $publicKey .= $component['tag'];
            $publicKey .= chr($component['length']);
            $publicKey .= hex2bin($component['content']);
        }

        $publicKey .= '-----END PUBLIC KEY-----';

        return $publicKey;
    }

    /**
     * Décode une chaîne base64url
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/').str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}
