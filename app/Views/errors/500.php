<?php
/* errors/500 — alias for errors/error with code 500 */
$code    = 500;
$message = $message ?? 'An unexpected error occurred. Please try again later.';
require __DIR__ . '/error.php';
