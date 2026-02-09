<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var \App\Services\DepWhatsappService $service */
$service = app(\App\Services\DepWhatsappService::class);

$res = $service->sendTemplateByName(
    '6285725681860',
    'notifikasi_tiket_open',
    'id',
    ['Nama','T-123','Judul tiket','Kategori','01 February 2026']
);

echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
