<?php
/**
 * Diagnostic script to check logo file existence in production
 * Run this in production to verify logo paths
 */

echo "=== DIABE Logo Diagnostic ===\n\n";

$paths = [
    'public_path' => public_path('images/diabe_logo.jpg'),
    'base_path_public' => base_path('public/images/diabe_logo.jpg'),
    'custom_public' => base_path('custom_public/images/diabe_logo.jpg'),
    'asset_url' => asset('images/diabe_logo.jpg'),
];

foreach ($paths as $label => $path) {
    if ($label === 'asset_url') {
        echo "$label: $path\n";
    } else {
        $exists = file_exists($path) ? '✓ EXISTS' : '✗ NOT FOUND';
        $readable = file_exists($path) && is_readable($path) ? '✓ READABLE' : '✗ NOT READABLE';
        echo "$label: $exists $readable\n";
        echo "  Path: $path\n";
        if (file_exists($path)) {
            echo "  Size: " . filesize($path) . " bytes\n";
        }
    }
}

echo "\n=== Environment ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "IS_DOCKER: " . (config('ninja.is_docker') ? 'YES' : 'NO') . "\n";
echo "PUBLIC_PATH: " . public_path() . "\n";
echo "BASE_PATH: " . base_path() . "\n";

echo "\n=== Directory Listing ===\n";
$publicImagesDir = public_path('images');
if (is_dir($publicImagesDir)) {
    $files = scandir($publicImagesDir);
    echo "Files in public/images:\n";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - $file\n";
        }
    }
} else {
    echo "public/images directory NOT FOUND\n";
}
