<?php

require './../vendor/autoload.php';

use app\Application;

$routerConfig = require './../config/router.php';
$dbConfig = require './../config/db.php';
$config = [
    'routerConfig' => $routerConfig,
    'dbConfig' => $dbConfig,
];
(new Application($config))->run();