<?php
// Simple test file to check if PHP and Laravel basics work
echo "PHP Version: " . phpversion() . "\n";
echo "Current Directory: " . getcwd() . "\n";

// Check if Laravel files exist
$files = [
    'vendor/autoload.php',
    'bootstrap/app.php', 
    'public/index.php',
    'artisan'
];

foreach ($files as $file) {
    echo $file . ": " . (file_exists($file) ? "✅ EXISTS" : "❌ MISSING") . "\n";
}

// Test autoloader
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
    echo "Autoloader: ✅ LOADED\n";
} else {
    echo "Autoloader: ❌ FAILED\n";
}

// Test Laravel bootstrap
try {
    if (file_exists('bootstrap/app.php')) {
        $app = require_once 'bootstrap/app.php';
        echo "Laravel Bootstrap: ✅ SUCCESS\n";
    } else {
        echo "Laravel Bootstrap: ❌ MISSING FILE\n";
    }
} catch (Exception $e) {
    echo "Laravel Bootstrap: ❌ FAILED - " . $e->getMessage() . "\n";
}