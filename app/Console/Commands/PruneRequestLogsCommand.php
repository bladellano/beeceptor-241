<?php

namespace App\Console\Commands;

use App\Models\RequestLog;
use Illuminate\Console\Command;

class PruneRequestLogsCommand extends Command
{
    protected $signature = 'requests:prune';

    protected $description = 'Remove request logs older than the configured retention period';

    public function handle(): int
    {
        $days = config('mock.request_log_retention_days', 7);
        $cutoff = now()->subDays($days);

        $deleted = RequestLog::query()
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$deleted} request log(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}
