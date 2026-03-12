<?php
/* errors/404 — alias for errors/error with code 404 */
$code    = 404;
$message = $message ?? 'The page you are looking for was not found.';
require __DIR__ . '/error.php';
