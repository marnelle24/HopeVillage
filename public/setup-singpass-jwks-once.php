<?php
/**
 * One-time setup: generate Singpass client JWKS and write SINGPASS_CLIENT_JWKS_JSON to .env
 *
 * 1. Set $secretToken below (or use a random string).
 * 2. Visit: https://hopevillage.sg/setup-singpass-jwks-once.php?token=YOUR_SECRET_TOKEN
 * 3. After success, DELETE this file and run: php artisan config:clear
 *
 * KEEP THE TOKEN SECRET. Anyone with the URL can overwrite your .env JWKS.
 */
$secretToken = 'amarasageapatgwapa1231'; // e.g. bin2hex(random_bytes(16))

if (!isset($_GET['token']) || !hash_equals($secretToken, $_GET['token'])) {
    header('Content-Type: text/plain');
    http_response_code(403);
    echo 'Forbidden. Use ?token=YOUR_SECRET_TOKEN';
    exit;
}

$projectRoot = __DIR__;
$envPath = $projectRoot . '/.env';

if (!is_file($projectRoot . '/vendor/autoload.php') || !is_readable($envPath)) {
    header('Content-Type: text/plain');
    http_response_code(500);
    echo 'Error: Laravel project root or .env not found.';
    exit;
}

// Generate EC P-256 key and JWKS (same as artisan singpass:generate-jwks)
$key = openssl_pkey_new([
    'private_key_type' => OPENSSL_KEYTYPE_EC,
    'curve_name' => 'prime256v1',
]);
if ($key === false) {
    header('Content-Type: text/plain');
    http_response_code(500);
    echo 'Error: Failed to generate EC key.';
    exit;
}
$details = openssl_pkey_get_details($key);
if (!$details || !isset($details['ec']['x'], $details['ec']['y'])) {
    header('Content-Type: text/plain');
    http_response_code(500);
    echo 'Error: Failed to get key details.';
    exit;
}
$base64url = function ($bin) {
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
};
$kid = 'hopevillage-sig-' . substr(bin2hex(random_bytes(8)), 0, 16);
$jwks = [
    'keys' => [
        [
            'kty' => 'EC',
            'crv' => 'P-256',
            'use' => 'sig',
            'alg' => 'ES256',
            'kid' => $kid,
            'x' => $base64url($details['ec']['x']),
            'y' => $base64url($details['ec']['y']),
        ],
    ],
];
$jsonOneLine = json_encode($jwks);

// Update .env: set or replace SINGPASS_CLIENT_JWKS_JSON (single-quoted so special chars are safe)
$envContent = file_get_contents($envPath);
$envValue = "'" . str_replace("'", "'\\''", $jsonOneLine) . "'";
$newLine = "SINGPASS_CLIENT_JWKS_JSON=" . $envValue . "\n";

if (preg_match('/^SINGPASS_CLIENT_JWKS_JSON=.*$/m', $envContent)) {
    $envContent = preg_replace('/^SINGPASS_CLIENT_JWKS_JSON=.*$/m', 'SINGPASS_CLIENT_JWKS_JSON=' . $envValue, $envContent);
} else {
    $envContent .= "\n" . $newLine;
}

if (file_put_contents($envPath, $envContent) === false) {
    header('Content-Type: text/plain');
    http_response_code(500);
    echo 'Error: Could not write .env';
    exit;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Singpass JWKS setup</title>
</head>
<body>
    <h1>Singpass client JWKS written to .env</h1>
    <p><strong>Next steps:</strong></p>
    <ol>
        <li><strong>Delete this file</strong>: <code>public/setup-singpass-jwks-once.php</code></li>
        <li>On the server run: <code>php artisan config:clear</code> (or <code>config:cache</code> if you use it)</li>
        <li>Confirm <a href="/.well-known/jwks.json">/.well-known/jwks.json</a> returns JSON (not an error)</li>
        <li>Register <code><?= htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'hopevillage.sg')) ?>/.well-known/jwks.json</code> in Singpass Developer Portal as Client JWKS URL</li>
    </ol>
    <p><strong>Security:</strong> Remove this script from the server after use.</p>
</body>
</html>
