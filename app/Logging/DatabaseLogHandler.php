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

            // Récupérer l'adresse IP et valider
            // $ipAddress = $context['ip_address'] ?? null;
            $ipAddress = filter_var($context['ip_address'] ?? null, FILTER_VALIDATE_IP) ? $context['ip_address'] : 'unknown';

            if (!$ipAddress || !filter_var($ipAddress, FILTER_VALIDATE_IP)) {
                $ipAddress = 'invalid';
            }
            

            // Récupérer l'adresse MAC
            $macAddress = $ipAddress ? $this->getMacAddress($ipAddress) : 'unavailable';

            DB::table('logs')->insert([
            'level' => $record->level->name,
            'action' => $context['action'] ?? 'Action inconnue',
            'message' => $context['message'] ?? $record->message,
            'user_id' => $context['user_id'] ?? null,
            'ip_address' => $ipAddress,
            'mac_address' => $macAddress,
            'status' => $context['status'] ?? 'unknown',
            'details' => json_encode($context['details'] ?? [], JSON_UNESCAPED_UNICODE),
            'context' => json_encode($context, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        } catch (\Exception $e) {
            Log::error('Erreur DatabaseLogger', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'context' => $record->context,
            ]);
        }
    }

    private function getMacAddress(string $ipAddress): ?string
    {
        $macAddress = null;

        // Valider la commande ARP disponible
        if (!shell_exec("which arp")) {
            Log::warning("La commande arp n'est pas disponible sur ce système.");
            return null;
        }

        if (PHP_OS === 'Linux' || PHP_OS === 'Darwin') {
            $arpResult = shell_exec("arp -n $ipAddress");
            preg_match('/([0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2})/i', $arpResult, $matches);
            $macAddress = $matches[1] ?? null;
        } elseif (PHP_OS === 'WINNT') {
            $arpResult = shell_exec("arp -a $ipAddress");
            preg_match('/([0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2})/i', $arpResult, $matches);
            $macAddress = $matches[1] ?? null;
        }

        return $macAddress;
    }
}
