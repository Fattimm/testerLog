<?php

namespace App\Http\Controllers;

use App\Traits\LogsActions;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogController extends Controller
{
    use LogsActions;

    /**
     * Liste des logs avec pagination.
     */
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
}
