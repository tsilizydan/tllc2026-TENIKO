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
