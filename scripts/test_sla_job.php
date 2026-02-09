<?php
require __DIR__ . '/../vendor/autoload.php';
// bootstrap app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Jobs\SendSlaReminder;
use Illuminate\Support\Facades\Log;

$ticketId = $argv[1] ?? null;
if (! $ticketId) {
    echo "Usage: php scripts/test_sla_job.php <ticketId>\n";
    exit(1);
}

echo "Running SendSlaReminder for ticket {$ticketId}\n";
$job = new SendSlaReminder((int)$ticketId);
try {
    $job->handle();
    echo "Job executed\n";
} catch (Throwable $e) {
    echo "Job failed: " . $e->getMessage() . "\n";
}
