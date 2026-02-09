<?php
// One-off test script to exercise DepWhatsappService (Mahadata sender)
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var \App\Services\DepWhatsappService $svc */
$svc = $app->make(\App\Services\DepWhatsappService::class);

echo "Calling sendText()...\n";
try {
    $res1 = $svc->sendText('6285725681860', 'Test pesan singkat dari scripted test.');
    var_export($res1);
} catch (Throwable $e) {
    echo "sendText error: " . $e->getMessage() . "\n";
}

echo "\nCalling sendTemplateByName()...\n";
try {
    $res2 = $svc->sendTemplateByName('6285725681860', 'sample_template_name', 'id', ['Param1','Param2','Param3']);
    var_export($res2);
} catch (Throwable $e) {
    echo "sendTemplateByName error: " . $e->getMessage() . "\n";
}

echo "\nDone. Check storage/logs/laravel.log for request/response details.\n";
