<?php
/**
 * Laravel Cache Clearer
 * 
 * Upload this file to your Laravel root directory via FTP
 * Access it via: https://hopevillage.sg/clear-cache.php?key=YOUR_SECRET_KEY
 * 
 * IMPORTANT: Delete this file after use for security!
 */

// Security: Set a secret key to prevent unauthorized access
$SECRET_KEY = 'change-this-to-a-random-string-12345';

// Check if key is provided and matches
$providedKey = $_GET['key'] ?? '';
if ($providedKey !== $SECRET_KEY) {
    http_response_code(403);
    die('Access denied. Invalid key.');
}

// Get Laravel base path (assuming this file is in the root)
$basePath = __DIR__;

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Laravel Cache Clearer</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .success { color: #4CAF50; background: #e8f5e9; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: #f44336; background: #ffebee; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #2196F3; background: #e3f2fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #ff9800; background: #fff3e0; padding: 10px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .file-path { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧹 Laravel Cache Clearer</h1>";

$results = [];
$errors = [];

// Function to delete directory recursively
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}

// Function to clear cache files
function clearCacheFiles($basePath, &$results, &$errors) {
    $cachePaths = [
        'config' => $basePath . '/bootstrap/cache/config.php',
        'routes' => $basePath . '/bootstrap/cache/routes-v7.php',
        'services' => $basePath . '/bootstrap/cache/services.php',
        'packages' => $basePath . '/bootstrap/cache/packages.php',
        'views' => $basePath . '/storage/framework/views',
    ];
    
    foreach ($cachePaths as $type => $path) {
        try {
            if (is_file($path)) {
                if (unlink($path)) {
                    $results[] = "✅ Cleared <strong>$type</strong> cache: " . basename($path);
                } else {
                    $errors[] = "❌ Failed to delete <strong>$type</strong> cache: " . basename($path);
                }
            } elseif (is_dir($path)) {
                if (deleteDirectory($path)) {
                    $results[] = "✅ Cleared <strong>$type</strong> cache directory: " . basename($path);
                } else {
                    $errors[] = "❌ Failed to delete <strong>$type</strong> cache directory: " . basename($path);
                }
            } else {
                $results[] = "ℹ️ <strong>$type</strong> cache not found (already cleared or doesn't exist)";
            }
        } catch (Exception $e) {
            $errors[] = "❌ Error clearing <strong>$type</strong> cache: " . $e->getMessage();
        }
    }
}

// Clear application cache
function clearApplicationCache($basePath, &$results, &$errors) {
    $cacheDir = $basePath . '/storage/framework/cache/data';
    if (is_dir($cacheDir)) {
        try {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            
            $deleted = 0;
            foreach ($files as $fileinfo) {
                if ($fileinfo->isDir()) {
                    rmdir($fileinfo->getRealPath());
                } else {
                    unlink($fileinfo->getRealPath());
                    $deleted++;
                }
            }
            $results[] = "✅ Cleared application cache ($deleted files)";
        } catch (Exception $e) {
            $errors[] = "❌ Error clearing application cache: " . $e->getMessage();
        }
    } else {
        $results[] = "ℹ️ Application cache directory not found";
    }
}

// Execute cache clearing
echo "<div class='info'>📂 Base path: <span class='file-path'>$basePath</span></div>";
echo "<div class='info'>🕐 Started at: " . date('Y-m-d H:i:s') . "</div>";

clearCacheFiles($basePath, $results, $errors);
clearApplicationCache($basePath, $results, $errors);

// Display results
echo "<h2>Results:</h2>";
foreach ($results as $result) {
    echo "<div class='success'>$result</div>";
}

if (!empty($errors)) {
    echo "<h2>Errors:</h2>";
    foreach ($errors as $error) {
        echo "<div class='error'>$error</div>";
    }
}

// Check if artisan exists and suggest running it
$artisanPath = $basePath . '/artisan';
if (file_exists($artisanPath)) {
    echo "<div class='warning'>
        <strong>⚠️ Note:</strong> This script clears cache files directly. 
        For best results, also run these commands via SSH if available:<br>
        <pre>php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear</pre>
    </div>";
}

echo "<div class='info'>✅ Cache clearing completed at: " . date('Y-m-d H:i:s') . "</div>";
echo "<div class='warning'><strong>🔒 Security:</strong> Please delete this file (clear-cache.php) after use!</div>";
echo "</div></body></html>";


