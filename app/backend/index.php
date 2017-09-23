<?php
//Read configuration and setup psr-4 class autoloader
require_once __DIR__.'/bootstrap.php';

//Instantiate and run application
$app = new \Test\Counters\Library\Application($config);
$app->run();