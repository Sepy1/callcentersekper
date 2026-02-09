<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

$payload = [
    'phone' => '6285725681860',
    'template_id' => '734693812466588',
    'language' => 'id',
    'params' => ['1','2','3','4'],
];

$request = Request::create('/', 'POST', $payload);

$controller = new \App\Http\Controllers\Api\WhatsappController();
/** @var \Illuminate\Http\Response $resp */
$resp = $controller->send($request);

echo "HTTP: " . $resp->getStatusCode() . PHP_EOL;
echo $resp->getContent() . PHP_EOL;
