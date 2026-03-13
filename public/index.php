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

// ── Error Configuration ───────────────────────────────────────
ini_set('display_errors', '0'); // Never display errors to users in production
ini_set('log_errors', '1');
error_reporting(E_ALL);

// ── Security Headers (PHP-level fallback for Namecheap) ───────
// These run even if mod_headers is disabled on shared hosting
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    // Remove PHP version fingerprint
    header_remove('X-Powered-By');
}

// ── Global Exception Handler (production-safe) ─────────────────
set_exception_handler(function (\Throwable $e) {
    // Always log the full details server-side
    error_log('[TENIKO] Uncaught ' . get_class($e) . ': ' . $e->getMessage()
        . ' in ' . $e->getFile() . ':' . $e->getLine()
        . ' — Trace: ' . str_replace("\n", ' | ', $e->getTraceAsString()));

    // Discard any partial output
    while (ob_get_level() > 0) { ob_end_clean(); }

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }

    // Generic user-facing error — no technical detail exposed
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
        . '<meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Something went wrong — TENIKO</title>'
        . '<style>*{box-sizing:border-box;margin:0;padding:0}'
        . 'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;'
        . 'background:#f9fafb;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:2rem}'
        . '.box{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:3rem;max-width:480px;text-align:center;box-shadow:0 12px 40px rgba(0,0,0,.08)}'
        . '.icon{font-size:3rem;margin-bottom:1rem}'
        . 'h1{font-size:1.5rem;color:#1a1f2e;margin-bottom:.75rem}'
        . 'p{color:#6b7280;line-height:1.6;margin-bottom:1.5rem}'
        . 'a{display:inline-block;background:#2E7D32;color:#fff;padding:.625rem 1.5rem;border-radius:9999px;text-decoration:none;font-weight:600}'
        . 'a:hover{background:#1B5E20}'
        . '</style></head><body>'
        . '<div class="box"><div class="icon">🌿</div>'
        . '<h1>Something went wrong</h1>'
        . '<p>We\'re sorry — an unexpected error occurred. Our team has been notified and we\'re working on a fix.</p>'
        . '<a href="/">Return to TENIKO</a></div></body></html>';
    exit;
});

// ── Global Error Handler ───────────────────────────────────────
set_error_handler(function (int $severity, string $msg, string $file, int $line): bool {
    if (!($severity & error_reporting())) return false;
    // Non-fatal: log and continue
    if ($severity & (E_NOTICE | E_USER_NOTICE | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT | E_WARNING | E_USER_WARNING)) {
        error_log("[TENIKO] {$msg} in {$file}:{$line}");
        return true;
    }
    // Fatal: escalate to exception handler
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
