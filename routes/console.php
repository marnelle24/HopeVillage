<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('singpass:generate-jwks', function () {
    $config = [
        'private_key_type' => OPENSSL_KEYTYPE_EC,
        'curve_name' => 'prime256v1', // P-256
    ];
    $key = openssl_pkey_new($config);
    if ($key === false) {
        $this->error('Failed to generate EC key: ' . openssl_error_string());
        return 1;
    }
    $details = openssl_pkey_get_details($key);
    if (!$details || !isset($details['ec']['x'], $details['ec']['y'])) {
        $this->error('Failed to get key details.');
        return 1;
    }
    $base64url = function (string $bin): string {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    };
    $kid = 'hopevillage-sig-' . substr(bin2hex(random_bytes(8)), 0, 16);
    $jwk = [
        'kty' => 'EC',
        'crv' => 'P-256',
        'use' => 'sig',
        'alg' => 'ES256',
        'kid' => $kid,
        'x' => $base64url($details['ec']['x']),
        'y' => $base64url($details['ec']['y']),
    ];
    $jwks = ['keys' => [$jwk]];
    $json = json_encode($jwks, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    $this->line('Add this to your .env as SINGPASS_CLIENT_JWKS_JSON (use one line, no newlines):');
    $this->newLine();
    $this->line(json_encode($jwks));
    $this->newLine();
    $this->line('Then register this URL in Singpass Developer Portal as Client JWKS URL:');
    $this->line('  ' . config('app.url', 'https://hopevillage.sg') . '/.well-known/jwks.json');
    return 0;
})->purpose('Generate EC P-256 key and output public JWKS for Singpass client JWKS URL (PX-E0101)');
