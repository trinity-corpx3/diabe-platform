<?php
/**
 * Script to restore the corrupted vat_number key back from rfc_number
 */

$files = [
    __DIR__ . '/public/react/es-Ca_e5WRV.json',
    __DIR__ . '/public/react/es_ES-jZFKIguI.json',
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }

    $content = file_get_contents($file);
    $original = $content;

    // Restore the key name
    $content = str_replace('"rfc_number":', '"vat_number":', $content);

    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Restored vat_number key in: " . basename($file) . "\n";
    } else {
        echo "No changes needed in: " . basename($file) . "\n";
    }
}

echo "Done!\n";
