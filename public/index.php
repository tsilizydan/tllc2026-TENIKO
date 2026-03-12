<?php
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', APP_PATH . '/Views');

// Load environment variables
$envFile = ROOT_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// ── Global exception / error handler ─────────────────────────
ini_set('display_errors', '0'); // Suppress PHP's own output; we handle it
ini_set('log_errors', '1');
error_reporting(E_ALL);

set_exception_handler(function (\Throwable $e) {
    // Log first
    error_log('[TENIKO] Uncaught ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    // Clean ALL output buffers so partial page HTML is discarded
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (!headers_sent()) http_response_code(500);

    // Show detailed error for diagnosis
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>500 Error</title></head><body>';
    echo '<div style="background:#fef2f2;border:2px solid #fca5a5;border-radius:8px;padding:2rem;margin:2rem;font-family:monospace;max-width:900px">';
    echo '<h2 style="color:#dc2626;margin:0 0 1rem">' . htmlspecialchars(get_class($e)) . '</h2>';
    echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
    echo '<details><summary style="cursor:pointer;color:#6b7280">Stack Trace</summary>';
    echo '<pre style="overflow:auto;font-size:11px;color:#374151;margin-top:.5rem">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</details>';
    echo '<p style="margin-top:1rem"><a href="/" style="color:#2563eb">Go Home</a></p>';
    echo '</div></body></html>';
});

// Only convert actual PHP Errors (not notices/warnings) into exceptions
set_error_handler(function (int $severity, string $msg, string $file, int $line): bool {
    if (!($severity & error_reporting())) return false;
    // Notices, deprecations, strict — just log, don't throw
    if ($severity & (E_NOTICE | E_USER_NOTICE | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT | E_WARNING | E_USER_WARNING)) {
        error_log("[TENIKO] {$msg} in {$file}:{$line}");
        return true;
    }
    throw new \ErrorException($msg, 0, $severity, $file, $line);
});

// Autoloader (PSR-4 style without Composer in dev)
spl_autoload_register(function (string $class): void {
    $prefix   = 'App\\';
    $base_dir = APP_PATH . '/';
    if (!str_starts_with($class, $prefix)) return;
    $relative = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) require $file;
});

// Also load Composer autoloader if available
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require ROOT_PATH . '/vendor/autoload.php';
}

// Load core helpers
require APP_PATH . '/Core/helpers.php';

// Bootstrap application
$config = require CONFIG_PATH . '/app.php';

// Start session
$session = new App\Core\Session();
$session->start();

// Load routes
$router = new App\Core\Router();
require CONFIG_PATH . '/routes.php';

// Dispatch request
$request = new App\Core\Request();
$router->dispatch($request);
