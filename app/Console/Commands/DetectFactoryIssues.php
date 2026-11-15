<?php

namespace App\Console\Commands;

use App\Services\Issues\IssueEngine;
use Illuminate\Console\Command;

class DetectFactoryIssues extends Command
{
    protected $signature = 'factory:detect-issues {--escalate : Also apply escalation rules}';

    protected $description = 'Detect shade, bundle, line, material, quality, and machine issues';

    public function handle(IssueEngine $engine): int
    {
        $created = $engine->scan();
        $this->components->info('Opened '.$created.' new factory issues.');

        if ($this->option('escalate')) {
            $escalated = $engine->escalate();
            $this->components->info('Escalated '.$escalated.' issues.');
        }

        return self::SUCCESS;
    }
}
