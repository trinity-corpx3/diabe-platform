<?php
$file = __DIR__ . '/public/react/es-Ca_e5WRV.json';
$content = file_get_contents($file);

// Find vat_number key and its value
if (preg_match('/"vat_number":"([^"]*)"/', $content, $matches)) {
    echo "Current value of vat_number: " . $matches[1] . "\n";
} else {
    echo "vat_number key not found\n";
}

// Now let's fix it to RFC
$content = preg_replace('/"vat_number":"[^"]*"/', '"vat_number":"RFC"', $content);
file_put_contents($file, $content);
echo "Updated vat_number to RFC\n";

// Do the same for es_ES
$file2 = __DIR__ . '/public/react/es_ES-jZFKIguI.json';
$content2 = file_get_contents($file2);
$content2 = preg_replace('/"vat_number":"[^"]*"/', '"vat_number":"RFC"', $content2);
file_put_contents($file2, $content2);
echo "Updated es_ES file as well\n";
