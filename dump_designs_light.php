<?php

use App\Models\Design;
use Illuminate\Support\Facades\DB;

$designs = DB::table('designs')->select('id', 'name', 'company_id')->get();

foreach ($designs as $design) {
    echo "ID: " . $design->id . " - Name: '" . $design->name . "' - Company ID: " . ($design->company_id ?? 'NULL') . "\n";
}
