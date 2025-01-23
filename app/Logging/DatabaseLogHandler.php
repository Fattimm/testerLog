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
            $context = $record->context;

            // Récupérer l'adresse IP du contexte
            $ipAddress = $context['ip_address'] ?? null;
            $macAddress = 'unavailable';

            // Si une adresse IP est disponible, tenter de récupérer l'adresse MAC
            if ($ipAddress) {
                $macAddress = $this->getMacAddress($ipAddress);
            }

            DB::table('logs')->insert([
                'level' => $record->level->name,
                'action' => $context['action'] ?? 'unknown',
                'message' => $record->message,
                'user_id' => $context['user_id'] ?? null,
                'ip_address' => $ipAddress,
                'mac_address' => $macAddress,
                'status' => $context['status'] ?? 'unknown',
                'details' => json_encode($context['details'] ?? [], JSON_UNESCAPED_UNICODE),
                'context' => json_encode($record->context, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DatabaseLogger: ' . $e->getMessage());
        }
    }

    /**
     * Récupère l'adresse MAC d'une machine à partir de son adresse IP.
     */
    private function getMacAddress(string $ipAddress): ?string
    {
        $macAddress = null;

        // Exécute la commande `arp` selon le système d'exploitation
        if (PHP_OS === 'Linux' || PHP_OS === 'Darwin') {
            // Pour Linux ou MacOS
            $arpResult = shell_exec("arp -n $ipAddress");
            preg_match('/([0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2})/i', $arpResult, $matches);
            $macAddress = $matches[1] ?? null;
        } elseif (PHP_OS === 'WINNT') {
            // Pour Windows
            $arpResult = shell_exec("arp -a $ipAddress");
            preg_match('/([0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2})/i', $arpResult, $matches);
            $macAddress = $matches[1] ?? null;
        }

        return $macAddress;
    }
}