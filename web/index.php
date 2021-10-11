<?php

// phpinfo();
use app\Application;
require './../app/Application.php';

$routerConfig = require './../config/router.php';
$config = [
    'routerConfig' => $routerConfig
];
(new Application($config))->run();