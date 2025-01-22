<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Monolog\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseLogHandler extends AbstractProcessingHandler
{
    public function __construct($level = Logger::DEBUG)
    {
        parent::__construct($level);
    }

    protected function write(LogRecord $record): void
    {
        try {
            // DB::table('logs')->insert([
            //     'level' => $record->level->name,
            //     'message' => $record->message,
            //     'context' => json_encode($record->context, JSON_UNESCAPED_UNICODE),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            $context = $record->context;

            DB::table('logs')->insert([
                'level' => $record->level->name, // Niveau du log
                'action' => $context['action'] ?? 'unknown', // Action effectuée
                'message' => $record->message, // Message principal
                'user_id' => $context['user_id'] ?? null, // Utilisateur (si disponible)
                'ip_address' => $context['ip_address'] ?? null, // Adresse IP (si disponible)
                'status' => $context['status'] ?? 'unknown', // Statut (succès ou échec)
                'details' => json_encode($context['details'] ?? [], JSON_UNESCAPED_UNICODE), // Détails
                'context' => json_encode($record->context, JSON_UNESCAPED_UNICODE), // Contexte complet
                'created_at' => now(), // Timestamp
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DatabaseLogger: ' . $e->getMessage());
        }
    }
}