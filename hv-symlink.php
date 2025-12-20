<?php
/**
 * Storage Symlink Creator
 * 
 * This script creates a symlink from public/storage to storage/app/public
 * 
 * SECURITY: Delete this file after creating the symlink!
 * 
 * Usage: Access via browser: https://yourdomain.com/symlink.php
 */

$basePath = dirname(__DIR__);
$publicPath = __DIR__;
$storagePath = $basePath . '/storage/app/public';
$symlinkPath = $publicPath . '/storage';

// HTML output function
function output($title, $message, $type = 'info') {
    $bgColor = $type === 'success' ? '#dfd' : ($type === 'error' ? '#fdd' : '#ddf');
    $borderColor = $type === 'success' ? '#0c0' : ($type === 'error' ? '#c00' : '#00c');
    
    echo "<div style='background: $bgColor; border: 1px solid $borderColor; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>$title:</strong><br>";
    echo $message;
    echo "</div>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Storage Symlink</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #856404;
        }
        .info {
            background: #e7f3ff;
            border: 1px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Storage Symlink Creator</h1>
        
        <div class="warning">
            <strong>⚠️ Security Warning:</strong> Delete this file (<code>symlink.php</code>) after creating the symlink!
        </div>

        <?php
        // Check if symlink already exists
        if (is_link($symlinkPath)) {
            $target = readlink($symlinkPath);
            if ($target === $storagePath || realpath($symlinkPath) === realpath($storagePath)) {
                output('Symlink Already Exists', "The symlink already exists and points to the correct location.", 'success');
                echo "<p><strong>Symlink Path:</strong> <code>$symlinkPath</code></p>";
                echo "<p><strong>Target:</strong> <code>$target</code></p>";
                echo "<p>✅ No action needed. You can safely delete this file.</p>";
                exit;
            } else {
                output('Symlink Exists But Points Elsewhere', "The symlink exists but points to: <code>$target</code>", 'error');
                echo "<p>You may need to delete the existing symlink first.</p>";
            }
        } elseif (file_exists($symlinkPath) || is_dir($symlinkPath)) {
            output('Directory Exists', "A directory already exists at <code>$symlinkPath</code>. Cannot create symlink.", 'error');
            echo "<p>Please delete or rename the existing directory first.</p>";
            exit;
        }

        // Check if storage/app/public directory exists
        if (!is_dir($storagePath)) {
            output('Storage Directory Missing', "The storage directory does not exist: <code>$storagePath</code>", 'error');
            echo "<p>Please create the directory structure first:</p>";
            echo "<ul>";
            echo "<li><code>storage/app/public/</code></li>";
            echo "</ul>";
            exit;
        }

        // Attempt to create symlink
        output('Attempting to Create Symlink', "Creating symlink from <code>$symlinkPath</code> to <code>$storagePath</code>", 'info');

        // Check if symlink function is available
        if (!function_exists('symlink')) {
            output('Symlink Function Not Available', "The PHP <code>symlink()</code> function is not available on this server.", 'error');
            echo "<p>This may be due to server configuration restrictions.</p>";
            exit;
        }

        // Try to create the symlink
        try {
            // On Windows, use different approach
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows doesn't support symlinks the same way
                if (!is_dir($symlinkPath)) {
                    if (!mkdir($symlinkPath, 0755, true)) {
                        throw new Exception("Failed to create directory");
                    }
                }
                output('Windows Detected', "On Windows, creating directory junction instead of symlink.", 'info');
            } else {
                // Unix/Linux - create actual symlink
                if (@symlink($storagePath, $symlinkPath)) {
                    output('Success!', "Symlink created successfully!", 'success');
                    echo "<p><strong>Symlink Path:</strong> <code>$symlinkPath</code></p>";
                    echo "<p><strong>Target:</strong> <code>$storagePath</code></p>";
                    
                    // Verify it works
                    if (is_link($symlinkPath) && file_exists($symlinkPath)) {
                        output('Verification', "✅ Symlink verified and accessible!", 'success');
                    } else {
                        output('Warning', "⚠️ Symlink created but verification failed. Please check manually.", 'error');
                    }
                } else {
                    $error = error_get_last();
                    output('Failed', "Failed to create symlink. Error: " . ($error['message'] ?? 'Unknown error'), 'error');
                    echo "<p><strong>Common causes:</strong></p>";
                    echo "<ul>";
                    echo "<li>Insufficient permissions (check directory permissions)</li>";
                    echo "<li>Symlink function disabled in PHP configuration</li>";
                    echo "<li>File system doesn't support symlinks</li>";
                    echo "</ul>";
                }
            }
        } catch (Exception $e) {
            output('Error', "Exception: " . $e->getMessage(), 'error');
        }

        // Display path information
        echo "<div class='info'>";
        echo "<h3>Path Information:</h3>";
        echo "<ul>";
        echo "<li><strong>Base Path:</strong> <code>$basePath</code></li>";
        echo "<li><strong>Public Path:</strong> <code>$publicPath</code></li>";
        echo "<li><strong>Storage Path:</strong> <code>$storagePath</code></li>";
        echo "<li><strong>Symlink Path:</strong> <code>$symlinkPath</code></li>";
        echo "</ul>";
        echo "</div>";

        // Check permissions
        echo "<div class='info'>";
        echo "<h3>Permission Check:</h3>";
        echo "<ul>";
        echo "<li>Public directory writable: " . (is_writable($publicPath) ? "✅ Yes" : "❌ No") . "</li>";
        echo "<li>Storage directory exists: " . (is_dir($storagePath) ? "✅ Yes" : "❌ No") . "</li>";
        echo "<li>Storage directory readable: " . (is_readable($storagePath) ? "✅ Yes" : "❌ No") . "</li>";
        echo "</ul>";
        echo "</div>";
        ?>

        <div class="warning" style="margin-top: 30px;">
            <strong>🔒 Important:</strong> After successfully creating the symlink, delete this file (<code>symlink.php</code>) for security reasons!
        </div>
    </div>
</body>
</html>