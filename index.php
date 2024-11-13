<?php
declare(strict_types=1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use core\App;
use core\ErrorHandler;
use core\Request;

if (preg_match('/\.(?!php|db$).+$/', $_SERVER["REQUEST_URI"])) {
    return false;  // сервер возвращает все файлы кроме указанных напрямую.
}

$app = new App(
    new Request(),
    new PDO('sqlite:' . __DIR__ . '/databases/database.db'),
    new ErrorHandler(),
);
$app->run();