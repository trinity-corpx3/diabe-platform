<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Account;

echo "--- VERIFICATION START ---\n";

// Create a dummy account (not saving to DB to avoid side effects, just testing the model logic)
$account = new Account();
$account->plan = 'free'; // Set to free to prove the override works

// Check White Label Feature
$hasWhiteLabel = $account->hasFeature(Account::FEATURE_WHITE_LABEL);
echo "Has White Label Feature (should be 1): " . ($hasWhiteLabel ? 'YES' : 'NO') . "\n";

// Check Remove Created By Feature
$hasRemoveCreatedBy = $account->hasFeature(Account::FEATURE_REMOVE_CREATED_BY);
echo "Has Remove 'Created By' Feature (should be 1): " . ($hasRemoveCreatedBy ? 'YES' : 'NO') . "\n";

echo "--- VERIFICATION END ---\n";
