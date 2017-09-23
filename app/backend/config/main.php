<?php
return [
    'application' => [
        'namespace_prefix' => 'Test\\Counters\\',
        'base_dir' => 'src',
        'is_debug' => true,
    ],
    'api' => [
        'access_control' => [
            'allow_methods' => [
                'GET',
                'POST'
            ],
            'allow_origin' => '*'
        ],
        'default_response_format' => 'json'
    ],
    'db' => [
        'is_enabled' => true,
        'dsn' => 'mysql:host=db;dbname=counters_db', #host=db - docker mariadb container's DNS name used
        'user' => 'root',
        'password' => 'root',
        'date_format' => 'Y-m-d'
    ],
    'routes' => require_once __DIR__.'/routes.php'
];