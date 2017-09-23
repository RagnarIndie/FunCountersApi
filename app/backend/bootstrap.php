<?php
$config = require_once __DIR__.'/config/main.php';

if ($config['application']['is_debug'] == true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
} else {
    ini_set('display_errors', 'off');
}

//Basic autoload closure
spl_autoload_register(function ($class) use ($config) {
    $prefix = $config['application']['namespace_prefix'];
    $base_dir = __DIR__."/{$config['application']['base_dir']}/";

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

//Global exception handler
//so we don't need to wrap everything with try...catch
set_exception_handler(function($exception) {
    switch ($exception->getCode()) {
        case 403:
            header('HTTP/1.0 403 Forbidden');
            break;
        case 404:
            header("HTTP/1.0 404 Not Found");
            break;
        case 405:
            header("HTTP/1.0 405 Method Not Allowed");
            break;
        default:
            header("HTTP/1.0 500 Internal Server Error");
            break;
    }

    $body = [
        'error' => $exception->getMessage()
    ];

    header('Content-Type: application/json');
    echo json_encode($body);
    exit();
});