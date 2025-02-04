<?php

namespace App\Traits;

trait LogsActions
{
    protected function addLogDetails(array $details)
    {
        // Stocker uniquement les détails métier
        request()->merge(['log_details' => $details]);
        
    }
}
