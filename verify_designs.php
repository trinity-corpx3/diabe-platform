<?php

use App\Models\Design;
use App\Models\Company;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying Design Filtering...\n";

// Test 1: Design::all() (should be unaffected by controller logic, but affected by global scopes if any)
$all = Design::count();
echo "Total Designs in DB: $all\n";

// Test 2: Simulate DesignController index with id=5
$filtered = Design::where('id', 5)->get();
echo "Designs with ID 5: " . $filtered->count() . "\n";
foreach ($filtered as $d) {
    echo " - " . $d->name . " (ID: " . $d->id . ")\n";
}

// Test 3: Simulate BaseController eager load
$company = Company::first();
if ($company) {
    echo "Checking Company (ID: " . $company->id . ") relations...\n";
    // We can't easily invoke the closure from controller, but we can replicate the logic
    // The relation 'designs' in Company.php has 'where('id', 5)' modded by me?
    // Let's check what $company->designs returns naturally
    $companyDesigns = $company->designs;
    echo "Company->designs count: " . $companyDesigns->count() . "\n";
    foreach ($companyDesigns as $d) {
        echo " - " . $d->name . " (ID: " . $d->id . ")\n";
    }
} else {
    echo "No company found.\n";
}
