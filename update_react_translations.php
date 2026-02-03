<?php
/**
 * Script to update React translation JSON files.
 * Uses string replacement to ensure "Vendedores" -> "Proveedores" and "CIF/NIF" -> "RFC".
 */

// Define the directory containing the React translation JSON files
$dir = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'react' . DIRECTORY_SEPARATOR;

// Find all Spanish translation files (and any others that might be relevant)
$files = glob($dir . 'es*.json');

echo "Found " . count($files) . " files to process.\n";

$replacements = [
    'Vendedores' => 'Proveedores',
    'Vendedor' => 'Proveedor',
    'vendedores' => 'proveedores',
    'vendedor' => 'proveedor',
    'CIF\\/NIF' => 'RFC', // Escaped slash in JSON
    'CIF/NIF' => 'RFC',   // Literal slash
    'CIF\\\/NIF' => 'RFC', // Triple escaped
    'NIF' => 'RFC',
    'CIF' => 'RFC',
    'VAT Number' => 'RFC',
    'vat_number' => 'rfc_number', // Just in case it's used as a label
];

foreach ($files as $file) {
    if (!file_exists($file))
        continue;

    echo "Processing " . basename($file) . "...\n";
    $content = file_get_contents($file);
    if ($content === false) {
        echo "Error reading file: $file\n";
        continue;
    }

    $new_content = $content;
    foreach ($replacements as $search => $replace) {
        $count = 0;
        $new_content = str_replace($search, $replace, $new_content, $count);
        if ($count > 0) {
            echo "  Replaced '$search' with '$replace' ($count times)\n";
        }
    }

    if ($new_content !== $content) {
        if (file_put_contents($file, $new_content) !== false) {
            echo "Successfully updated " . basename($file) . "\n";
        } else {
            echo "Error writing to file: $file\n";
        }
    } else {
        echo "  No changes needed for " . basename($file) . "\n";
    }
}

echo "Done!\n";
