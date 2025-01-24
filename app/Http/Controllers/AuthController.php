<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

class AuthController extends Controller
{
    /**
     * Connexion d'un utilisateur
     */

    public function login(Request $request)
    {
        
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Tenter de connecter l'utilisateur
            if (!Auth::attempt($credentials)) {
                // Log d'échec de connexion
                LogService::warning('Échec de connexion', [
                    'action' => 'login',
                    'status' => 'error',
                    'email' => $request->email,
                    'ip_address' => $request->ip(),
                    // 'mac_address' => $macAddress,
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'details' => [
                        'email' => $request->email,
                    ],
                ]);

                return response()->json([
                    'message' => 'Erreur d\'authentification : Email ou mot de passe incorrect',
                ], 401);
            }

            // Récupérer l'utilisateur connecté
            $user = Auth::user();

            // Générer un token d'accès
            $token = $user->createToken('Personal Access Token')->accessToken;

            // Log de succès de connexion
            LogService::info('Connexion réussie', [
                'action' => 'login',
                'status' => 'success',
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
                // 'mac_address' => $macAddress,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'details' => [
                    'email' => $request->email,
                ],
            ]);

            return response()->json([
                'message' => 'Connexion réussie',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            // Log d'erreur
            LogService::error('Erreur lors de la tentative de connexion', [
                'action' => 'login',
                'status' => 'error',
                'email' => $request->email ?? 'inconnu',
                'ip_address' => $request->ip(),
                'error_message' => $e->getMessage(),
                // 'mac_address' => $macAddress,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'details' => [
                    'email' => $request->email,
                ],
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la connexion',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Révoquer le token actif
            $token = $request->user()->token();
            $token->revoke();

            // Log de succès de déconnexion
            LogService::info('Déconnexion réussie', [
                'action' => 'logout',
                'status' => 'success',
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'ip_address' => $request->ip(),
                // 'mac_address' => $macAddress,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'details' => [
                    'email' => $request->email,
                ],
            ]);

            return response()->json([
                'message' => 'Déconnexion réussie',
            ]);
        } catch (\Exception $e) {
            // Log d'erreur
            LogService::error('Erreur lors de la tentative de déconnexion', [
                'action' => 'logout',
                'status' => 'error',
                'user_id' => $request->user()->id ?? 'inconnu',
                'email' => $request->user()->email ?? 'inconnu',
                'ip_address' => $request->ip(),
                'error_message' => $e->getMessage(),
                // 'mac_address' => $macAddress,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'details' => [
                    'email' => $request->email,
                ],
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la déconnexion',
            ], 500);
        }
    }
}
