<?php
error_reporting(E_ALL & ~E_DEPRECATED);

require __DIR__ . '/vendor/autoload.php';

use core\Application;

$app = new Application();

$app->run();