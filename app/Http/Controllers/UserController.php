<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\LogService;
use App\Repositories\UserRepository;


class UserController extends Controller
{
    protected $userRepository;

    // Injection du repository via le constructeur
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Crée un nouvel utilisateur.
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        DB::beginTransaction();
        try {
            $user = $this->userRepository->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Appel au service de logging
            LogService::info('Nouvel uuserstilisateur créé', [
                'action' => 'create_user',
                'status' => 'success',
                'user_id' => optional(auth()->user())->id,
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'details' => [
                    'created_user_id' => $user->id,
                    'created_user_name' => $user->name,
                ],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Utilisateur créé avec succès',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            LogService::error('Erreur lors de la création d\'un utilisateur', [
                'action' => 'create_user',
                'status' => 'error',
                'user_id' => optional(auth()->user())->id,
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erreur lors de la création de l\'utilisateur',
            ], 500);
        }
    }

    /**
     * Liste tous les utilisateurs.
     */
    public function listUsers(Request $request)
    {
        // Récupérer tous les utilisateurs
        $users = $this->userRepository->getAll();


        // Log l'événement avec plus de contexte
        LogService::info('Liste des utilisateurs récupérée', [
            'action' => 'list_users',
            'status' => 'success',
            'user_id' => optional(auth()->user())->id,
            'ip_address' => $request->ip(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'details' => [
                'total_users' => $users->count(),
            ],
        ]);

        return response()->json([
            'message' => 'Liste des utilisateurs récupérée avec succès',
            'users' => $users,
        ]);
    }

    /**
     * Supprime un utilisateur par ID.
     */
    public function deleteUser($id, Request $request)
    {
        $user = $this->userRepository->findById($id);

        if ($user) {
            DB::beginTransaction(); // Commence une transaction
            try {
                // Supprimer l'utilisateur
                $user->delete();

                // Log de la suppression avec plus de contexte
                LogService::info('Utilisateur supprimé avec succès', [
                    'action' => 'delete_user',
                    'status' => 'success',
                    'user_id' => optional(auth()->user())->id,
                    'ip_address' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'details' => [
                        'deleted_user_id' => $user->id,
                        'deleted_user_name' => $user->name,
                    ],
                ]);

                DB::commit(); // Valide la transaction

                return response()->json([
                    'message' => 'Utilisateur supprimé avec succès',
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack(); // Annule la transaction en cas d'erreur
                LogService::error('Erreur lors de la suppression de l\'utilisateur', [
                    'action' => 'delete_user',
                    'status' => 'error',
                    'user_id' => optional(auth()->user())->id,
                    'ip_address' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'error_message' => $e->getMessage(),
                ]);

                return response()->json([
                    'message' => 'Erreur lors de la suppression de l\'utilisateur',
                ], 500);
            }
        } else {
            // Log si l'utilisateur est introuvable
            LogService::warning('Tentative de suppression d\'un utilisateur introuvable', [
                'action' => 'delete_user',
                'status' => 'error',
                'user_id' => optional(auth()->user())->id,
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'details' => [
                    'deleted_user_id' => $id,
                    'error_message' => 'Utilisateur introuvable',
                ],
            ]);

            return response()->json([
                'message' => 'Utilisateur introuvable',
            ], 404);
        }
    }
}
