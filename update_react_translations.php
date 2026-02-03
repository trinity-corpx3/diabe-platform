<?php

$files = [
    'public/react/es-Ca_e5WRV.json',
    'public/react/es_ES-jZFKIguI.json',
];

$replacements = [
    '"vendors":"Vendedores"' => '"vendors":"Proveedores"',
    '"vat_number":"CIF\/NIF"' => '"vat_number":"RFC"',
    '"list_vendors":"Listar Proveedores"' => '"list_vendors":"Listar Proveedores"', // Correcting plural
    '"archived_vendors":":count proveedores actualizados con éxito"' => '"archived_vendors":":count proveedores actualizados con éxito"', // No change needed if already Proveedores
    '"deleted_vendors":":count proveedores actualizados con éxito"' => '"deleted_vendors":":count proveedores actualizados con éxito"', // No change needed
    '"vendor":"Proveedor"' => '"vendor":"Proveedor"', // Standardizing singular
];

// Broad search and replace for any "Vendedores" or "CIF/NIF" outside specific JSON keys too
$patterns = [
    '/Vendedores/' => 'Proveedores',
    '/CIF\/NIF/' => 'RFC',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);

        $original = $content;

        // Use patterns for thoroughness
        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        if ($content !== $original) {
            file_put_contents($path, $content);
            echo "Updated $file\n";
        } else {
            echo "No changes needed for $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
