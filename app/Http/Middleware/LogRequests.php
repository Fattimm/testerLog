<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LogService;

class LogRequests
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);
        $endTime = microtime(true);

        // Récupérer les informations de l'action
        $actionInfo = $this->getActionInfo($request);

        // Données de base pour le log
        $logData = [
            // 'ip_address' => $request->ip(),
            'ip_address' => $this->getRealIp($request),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status' => $response->status(),
            'duration' => round(($endTime - $startTime) * 1000, 2) . ' ms',
            'user_id' => $request->user()?->id
        ];

        // Si on a une configuration pour cette action
        if ($actionInfo) {
            $isSuccess = $response->status() < 400;
            $message = $isSuccess ?
                ($actionInfo['success_message'] ?? 'Opération réussie') : ($actionInfo['error_message'] ?? 'Échec de l\'opération');

            $logData = array_merge($logData, [
                'action' => $actionInfo['action'],
                'status' => $isSuccess ? 'success' : 'error',
                'message' => $message,
                'details' => $this->extractDetails($request, $actionInfo['details'])
            ]);
        } else {
            // Log générique pour les routes non configurées
            $logData['action'] = 'Requête non catégorisée';
            $logData['status'] = $response->status() < 400 ? 'success' : 'error';
        }

        // Ajouter les détails supplémentaires si présents
        if ($request->has('log_details')) {
            $logData['details'] = array_merge(
                $logData['details'] ?? [],
                $request->get('log_details')
            );
        }

        LogService::info($logData['message'], $logData);

        return $response;
    }

    private function getActionInfo(Request $request)
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        $controller = get_class($route->getController());
        $action = $route->getActionMethod();

        return config("logging_actions.{$controller}.{$action}");
    }

    private function extractDetails(Request $request, array $detailKeys)
    {
        $details = [];
        foreach ($detailKeys as $key) {
            if ($request->has($key)) {
                $details[$key] = $request->input($key);
            }
        }
        return $details;
    }

    private function getRealIp(Request $request)
    {
        $ip = $request->header('X-Forwarded-For') ?? $request->header('X-Real-IP') ?? $request->ip();

        // Supprime les espaces et garde la première IP si plusieurs sont listées
        $ip = explode(',', $ip)[0];
        $ip = trim($ip);

        // Vérifie si c'est une IP valide
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return 'IP non valide';
        }

        return $ip;
    }
}
