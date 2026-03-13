<?php
/**
 * TENIKO Debug — Read PHP Error Log
 * Temporary diagnostic endpoint — REMOVE after debugging
 */

// Only allow access from known debug IPs or with a secret token
$secret = 'teniko-debug-1337';
if (($_GET['t'] ?? '') !== $secret) {
    http_response_code(403);
    die('Forbidden');
}

header('Content-Type: text/plain; charset=UTF-8');
$logFile = ini_get('error_log');
echo "=== PHP Error Log: {$logFile} ===\n\n";
if ($logFile && file_exists($logFile)) {
    $lines = file($logFile);
    $last200 = array_slice($lines, -200);
    foreach ($last200 as $line) {
        if (strpos($line, 'TENIKO') !== false || strpos($line, 'dialect') !== false || strpos($line, 'Dialect') !== false) {
            echo $line;
        }
    }
    echo "\n\n=== ALL LAST 50 LINES ===\n";
    $last50 = array_slice($lines, -50);
    foreach ($last50 as $line) echo $line;
} else {
    echo "Error log not found at: {$logFile}\n";
    echo "ini_get error_log: " . var_export($logFile, true) . "\n";
    echo "PHP version: " . phpversion() . "\n";
    echo "display_errors: " . ini_get('display_errors') . "\n";
    echo "display_startup_errors: " . ini_get('display_startup_errors') . "\n";
}

echo "\n\n=== PHP INFO ===\n";
echo "Version: " . PHP_VERSION . "\n";
echo "OS: " . PHP_OS . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "Error reporting: " . error_reporting() . "\n";
echo "Error log: " . ini_get('error_log') . "\n";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
