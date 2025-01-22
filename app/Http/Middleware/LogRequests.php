<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    public function handle($request, Closure $next)
    {
        // Avant d'envoyer la requête au contrôleur
        Log::channel('database')->info('Incoming Request', [
            'action' => 'request_received',
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'payload' => $request->all(),
            'ip_address' => $request->ip(),
            'user_id' => optional(auth()->user())->id,
        ]);

        $response = $next($request);

        // Après avoir reçu la réponse
        Log::channel('database')->info('Outgoing Response', [
            'action' => 'response_sent',
            'status' => $response->status(),
            'ip_address' => $request->ip(),
            'user_id' => optional(auth()->user())->id,
        ]);

        return $response;
    }
}