<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authentifier un utilisateur et retourner un token API (Sanctum).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Identifiants invalides',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Ce compte est désactivé',
            ], 403);
        }

        $deviceName = $credentials['device_name'] ?? ($request->userAgent() ?: 'api');

        // Révoquer les anciens tokens pour ce device (optionnel)
        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->transformUser($user),
        ]);
    }

    /**
     * Retourner l'utilisateur authentifié via le token.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $this->transformUser($user),
        ]);
    }

    /**
     * Déconnecter l'utilisateur pour le token courant.
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Déconnecté avec succès',
        ]);
    }

    /**
     * Déconnecter l'utilisateur de tous ses appareils (révoquer tous les tokens).
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Tous les tokens ont été révoqués',
        ]);
    }

    /**
     * Transformer l'utilisateur pour la réponse API.
     */
    private function transformUser(User $user): array
    {
        return [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'roles' => $user->getRoleNames(),
        ];
    }
}

