<?php

namespace App\Logging;

use Monolog\Logger;
use App\Logging\DatabaseLogHandler;

class DatabaseLogger
{
    /**
     * Crée une instance de Monolog avec notre handler personnalisé.
     *
     * @param array $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $logger = new Logger('database');
        $logger->pushHandler(new DatabaseLogHandler());
        return $logger;
    }
}
