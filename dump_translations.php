<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Company;

foreach (Company::all() as $company) {
    echo "ID: " . $company->id . " | Name: " . $company->present()->name() . PHP_EOL;
    echo "Translations: " . json_encode($company->settings->translations) . PHP_EOL;
    echo "-----------------------------------" . PHP_EOL;
}
