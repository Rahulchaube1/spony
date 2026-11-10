<?php

// Set VERCEL environment indicators
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

$incomingHost = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? null;
if (!empty($incomingHost)) {
    $proto = ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'https') === 'http' ? 'http' : 'https';
    $dynamicAppUrl = $proto . '://' . $incomingHost;
    putenv("APP_URL={$dynamicAppUrl}");
    $_ENV['APP_URL'] = $dynamicAppUrl;
    $_SERVER['APP_URL'] = $dynamicAppUrl;
}

putenv('DB_CONNECTION=sqlite');
$_ENV['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_CONNECTION'] = 'sqlite';

putenv('DB_DATABASE=/tmp/database.sqlite');
$_ENV['DB_DATABASE'] = '/tmp/database.sqlite';
$_SERVER['DB_DATABASE'] = '/tmp/database.sqlite';

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

// Copy pre-populated SQLite database to writable /tmp if missing or empty
$sourceDb = __DIR__ . '/../database/database.sqlite';
$targetDb = '/tmp/database.sqlite';
if (file_exists($sourceDb) && (!file_exists($targetDb) || filesize($targetDb) === 0 || filemtime($sourceDb) > filemtime($targetDb))) {
    @copy($sourceDb, $targetDb);
    @chmod($targetDb, 0666);
}

// Ensure REMOTE_ADDR is set for serverless environments
if (empty($_SERVER['REMOTE_ADDR'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '127.0.0.1';
}

// Forward Vercel request to Laravel public index
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    header('Content-Type: text/plain', true, 500);
    echo "VYNTRA APPLICATION ERROR: " . $e->getMessage() . "\n\nFile: " . $e->getFile() . ":" . $e->getLine() . "\n\nTrace:\n" . $e->getTraceAsString();
}
