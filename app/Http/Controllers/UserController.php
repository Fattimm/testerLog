<?php

namespace App\Http\Controllers;

use App\Traits\LogsActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    use LogsActions;

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        // Validation des paramètres de pagination si nécessaire
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            $this->addLogDetails([
                'validation_errors' => $validator->errors()->toArray(),
                'submitted_data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Erreur de validation des paramètres',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $perPage = $request->input('per_page', 20);
            
            $logs = DB::table('logs')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $this->addLogDetails([
                'total_logs' => $logs->total(),
                'current_page' => $logs->currentPage(),
                'per_page' => $logs->perPage()
            ]);

            return response()->json([
                'message' => 'Liste des logs',
                'logs' => $logs,
            ]);

        } catch (\Exception $e) {
            $this->addLogDetails([
                'error_message' => $e->getMessage(),
                'requested_page' => $request->input('page'),
                'requested_per_page' => $request->input('per_page')
            ]);

            return response()->json([
                'message' => 'Erreur lors de la récupération des logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listUsers(Request $request)
    {
        $users = $this->userRepository->getAll();

        // Ajoute les détails pour le log
        $this->addLogDetails([
            'total_users' => count($users)
        ]);

        return response()->json([
            'message' => 'Liste des utilisateurs récupérée avec succès',
            'users' => $users,
        ]);
    }

    public function deleteUser($id)
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            $this->addLogDetails([
                'attempted_user_id' => $id,
                'error' => 'Utilisateur introuvable'
            ]);

            return response()->json([
                'message' => 'Utilisateur introuvable',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $userName = $user->name; // Capture le nom avant la suppression
            $user->delete();

            $this->addLogDetails([
                'deleted_user_id' => $id,
                'deleted_user_name' => $userName
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Utilisateur supprimé avec succès',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->addLogDetails([
                'error_message' => $e->getMessage(),
                'failed_user_id' => $id
            ]);

            return response()->json([
                'message' => 'Erreur lors de la suppression de l\'utilisateur',
            ], 500);
        }
    }
}
