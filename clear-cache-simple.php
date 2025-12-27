<?php
/**
 * Simple Laravel Cache Clearer
 * 
 * Upload to Laravel root directory via FTP
 * Access via browser: https://hopevillage.sg/clear-cache-simple.php
 * 
 * DELETE THIS FILE AFTER USE!
 */

// Simple password protection (change this!)
$PASSWORD = 'clear123'; // Change this to something secure!

// Check password
if (!isset($_GET['pass']) || $_GET['pass'] !== $PASSWORD) {
    die('Access denied. Add ?pass=YOUR_PASSWORD to the URL');
}

$basePath = __DIR__;

echo "<h1>Clearing Laravel Caches...</h1>";
echo "<pre>";

// Clear config cache
$configCache = $basePath . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    echo "✅ Config cache cleared\n";
} else {
    echo "ℹ️ Config cache not found\n";
}

// Clear route cache
$routeCache = $basePath . '/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "✅ Route cache cleared\n";
} else {
    echo "ℹ️ Route cache not found\n";
}

// Clear services cache
$servicesCache = $basePath . '/bootstrap/cache/services.php';
if (file_exists($servicesCache)) {
    unlink($servicesCache);
    echo "✅ Services cache cleared\n";
} else {
    echo "ℹ️ Services cache not found\n";
}

// Clear packages cache
$packagesCache = $basePath . '/bootstrap/cache/packages.php';
if (file_exists($packagesCache)) {
    unlink($packagesCache);
    echo "✅ Packages cache cleared\n";
} else {
    echo "ℹ️ Packages cache not found\n";
}

// Clear view cache directory
$viewsCache = $basePath . '/storage/framework/views';
if (is_dir($viewsCache)) {
    $files = glob($viewsCache . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ View cache cleared\n";
} else {
    echo "ℹ️ View cache directory not found\n";
}

// Clear application cache
$appCache = $basePath . '/storage/framework/cache/data';
if (is_dir($appCache)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($appCache, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($files as $fileinfo) {
        if ($fileinfo->isDir()) {
            rmdir($fileinfo->getRealPath());
        } else {
            unlink($fileinfo->getRealPath());
        }
    }
    echo "✅ Application cache cleared\n";
} else {
    echo "ℹ️ Application cache directory not found\n";
}

echo "\n✅ All caches cleared!\n";
echo "\n⚠️ DELETE THIS FILE NOW for security!\n";
echo "</pre>";

