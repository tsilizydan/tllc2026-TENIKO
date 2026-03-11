<?php
return [
    'driver'   => 'mysql',
    'host'     => $_ENV['DB_HOST']  ?? 'localhost',
    'port'     => $_ENV['DB_PORT']  ?? '3306',
    'name'     => $_ENV['DB_NAME']  ?? 'tsilscpx_teniko',
    'user'     => $_ENV['DB_USER']  ?? 'tsilscpx_chibi_admin',
    'password' => $_ENV['DB_PASS']  ?? '9@UPN~I@O]Dw',
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
