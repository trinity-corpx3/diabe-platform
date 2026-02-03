<?php

use App\Models\Company;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Just get one or two companies to avoid memory issues
$companies = Company::take(5)->get();

foreach ($companies as $company) {
    echo "ID: " . $company->id . "\n";
    echo "Name: " . $company->present()->name() . "\n";
    echo "Translations: " . json_encode($company->settings->translations ?? [], JSON_UNESCAPED_UNICODE) . "\n";
    echo "-------------------\n";
}
