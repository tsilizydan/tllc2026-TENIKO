<?php
return [
    'name'     => $_ENV['APP_NAME']  ?? 'TENIKO',
    'url'      => $_ENV['APP_URL']   ?? 'http://teniko.tsilizy.com',
    'env'      => $_ENV['APP_ENV']   ?? 'production',
    'debug'    => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'key'      => $_ENV['APP_KEY']   ?? '',
    'timezone' => 'Indian/Antananarivo',
    'locale'   => 'mg',
    'fallback_locale' => 'fr',
    'supported_locales' => ['mg', 'fr', 'en'],
];
