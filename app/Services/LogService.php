<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LogService
{
    /**
     * Log une information.
     *
     * @param string $message
     * @param array $context
     */
    public static function info(string $message, array $context = [])
    {
        Log::channel('stack')->info($message, $context);
    }

    /**
     * Log une erreur.
     *
     * @param string $message
     * @param array $context
     */
    public static function error(string $message, array $context = [])
    {
        Log::channel('stack')->error($message, $context);
    }

    /**
     * Log un avertissement.
     *
     * @param string $message
     * @param array $context
     */
    public static function warning(string $message, array $context = [])
    {
        Log::channel('stack')->warning($message, $context);
    }
}
