<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

$names = ['Deposito', 'deposito'];
$deleted = 0;
foreach ($names as $name) {
    $count = Category::where('name', $name)->delete();
    $deleted += $count;
}

if ($deleted) {
    echo "Deleted $deleted category(ies) named 'Deposito'.\n";
} else {
    echo "No category named 'Deposito' found.\n";
}

return 0;
