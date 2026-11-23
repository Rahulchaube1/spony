<?php

// Set VERCEL environment indicators
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv('APP_DEBUG=false');
$_ENV['APP_DEBUG'] = 'false';
$_SERVER['APP_DEBUG'] = 'false';

putenv('COOLIFY_VERSION=1.0.0');
$_ENV['COOLIFY_VERSION'] = '1.0.0';
$_SERVER['COOLIFY_VERSION'] = '1.0.0';

// An infrastructure console needs persistent storage and its own encryption key.
if (!getenv('APP_KEY') || !getenv('DATABASE_URL')) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Vyntra is temporarily unavailable while the workspace is being configured.';
    return;
}

putenv('APP_MAINTENANCE_DRIVER=file');
$_ENV['APP_MAINTENANCE_DRIVER'] = 'file';
$_SERVER['APP_MAINTENANCE_DRIVER'] = 'file';

putenv('APP_MAINTENANCE_STORE=array');
$_ENV['APP_MAINTENANCE_STORE'] = 'array';
$_SERVER['APP_MAINTENANCE_STORE'] = 'array';

putenv('CACHE_STORE=array');
$_ENV['CACHE_STORE'] = 'array';
$_SERVER['CACHE_STORE'] = 'array';

putenv('CACHE_DRIVER=array');
$_ENV['CACHE_DRIVER'] = 'array';
$_SERVER['CACHE_DRIVER'] = 'array';

putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';

putenv('QUEUE_CONNECTION=sync');
$_ENV['QUEUE_CONNECTION'] = 'sync';
$_SERVER['QUEUE_CONNECTION'] = 'sync';

// Ensure storage subdirectories exist in /tmp for serverless environment
$storageDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Ensure REMOTE_ADDR is set for serverless environments
if (empty($_SERVER['REMOTE_ADDR'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '127.0.0.1';
}

// Forward Vercel request to Laravel public index
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $error) {
    error_log('Vyntra could not serve the request: ' . get_class($error));
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Vyntra could not complete this request.';
}
