<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanLogs extends Command
{
    protected $signature = 'logs:clean';
    protected $description = 'Clean old logs from the database';

    public function handle()
    {
        DB::table('logs')->where('created_at', '<', now()->subDays(30))->delete();
        $this->info('Old logs cleaned successfully.');
    }
}
