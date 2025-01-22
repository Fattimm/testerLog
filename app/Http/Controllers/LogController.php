<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    /**
     * Liste des logs avec pagination.
     */
    public function index()
    {
        // Récupérer les logs avec pagination (par exemple, 20 logs par page)
        $logs = DB::table('logs')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Retourner les logs au format JSON
        return response()->json([
            'message' => 'Liste des logs',
            'logs' => $logs,
        ]);
    }
}
