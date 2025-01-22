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
            LogService::info('Nouvel utilisateur créé', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'action' => 'create_user',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Utilisateur créé avec succès',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            LogService::error('Erreur lors de la création d\'un utilisateur', [
                'error_message' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'action' => 'create_user',
            ]);

            return response()->json([
                'message' => 'Erreur lors de la création de l\'utilisateur',
            ], 500);
        }
    }

    /**
     * Liste tous les utilisateurs.
     */
    public function listUsers()
    {
        // Récupérer tous les utilisateurs
        $users = $this->userRepository->getAll();


        // Log l'événement avec plus de contexte
        LogService::info('Liste des utilisateurs récupérée', [
            'count' => $users->count(),
            'ip_address' => request()->ip(),
            'action' => 'list_users',
        ]);

        return response()->json([
            'message' => 'Liste des utilisateurs récupérée avec succès',
            'users' => $users,
        ]);
    }

    /**
     * Supprime un utilisateur par ID.
     */
    public function deleteUser($id)
    {
        $user = $this->userRepository->findById($id);

        if ($user) {
            DB::beginTransaction(); // Commence une transaction
            try {
                // Supprimer l'utilisateur
                $user->delete();

                // Log de la suppression avec plus de contexte
                LogService::info('Utilisateur supprimé', [
                    'deleted_user_id' => $user->id,
                    'deleted_user_name' => $user->name,
                    'ip_address' => request()->ip(),
                    'action' => 'delete_user',
                ]);

                DB::commit(); // Valide la transaction

                return response()->json([
                    'message' => 'Utilisateur supprimé avec succès',
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack(); // Annule la transaction en cas d'erreur
                LogService::error('Erreur lors de la suppression de l\'utilisateur', [
                    'error_message' => $e->getMessage(),
                    'ip_address' => request()->ip(),
                    'action' => 'delete_user',
                ]);

                return response()->json([
                    'message' => 'Erreur lors de la suppression de l\'utilisateur',
                ], 500);
            }
        } else {
            // Log si l'utilisateur est introuvable
            LogService::warning('Tentative de suppression d\'un utilisateur introuvable', [
                'user_id' => $id,
                'ip_address' => request()->ip(),
                'action' => 'delete_user',
                'status' => 'not_found',
            ]);

            return response()->json([
                'message' => 'Utilisateur introuvable',
            ], 404);
        }
    }
}
