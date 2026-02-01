<?php

use App\Models\Design;

$designs = Design::all();

foreach ($designs as $design) {
    echo "ID: " . $design->id . " - Name: " . $design->name . " - Company ID: " . ($design->company_id ?? 'NULL') . "\n";
}
