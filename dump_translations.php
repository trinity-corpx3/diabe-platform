<?php

use App\Models\Company;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$companies = Company::all();

$results = [];

foreach ($companies as $company) {
    $results[] = [
        'id' => $company->id,
        'name' => $company->present()->name(),
        'settings' => $company->settings,
        'custom_fields' => $company->custom_fields,
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
