<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Design;
use App\Models\Company;

echo "\n--- VERIFICATION OF DESIGN FILTERING ---\n";

// 1. Check Database State
$total = Design::count(); // Count of non-deleted designs
$business = Design::where('id', 5)->first();
$others = Design::where('id', '!=', 5)->count();

echo "Total Active Designs in DB: $total\n";
echo "Business Design (ID 5): " . ($business ? "EXISTS" : "MISSING") . "\n";
echo "Other Designs Count: $others (Should be 0 if migration ran)\n";

if ($others > 0) {
    echo "WARNING: Migration has NOT successfully soft-deleted other designs yet.\n";
} else {
    echo "SUCCESS: Only 'Business' design remains active in the database.\n";
}

// 2. Check Company Relationship
$company = Company::first();
if ($company) {
    echo "\nChecking Company->designs relationship...\n";
    $relDesigns = $company->designs;
    echo "Count: " . $relDesigns->count() . "\n";
    foreach ($relDesigns as $d) {
        echo " - " . $d->name . " (ID: " . $d->id . ")\n";
    }
}
echo "\n----------------------------------------\n";
