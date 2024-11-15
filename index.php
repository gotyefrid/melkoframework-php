<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use core\App;
use core\ErrorHandler;
use core\Request;

if (preg_match('/\.(?!php|db$).+$/', $_SERVER["REQUEST_URI"])) {
    return false;  // server returns all files except those specified directly
}

$app = new App(
    new Request(),
    new PDO('sqlite:' . __DIR__ . '/databases/database.db'),
    new ErrorHandler(true),
    false
);
$app->run();


/**
 * For global access to an application with a short name
 * @return App
 */
function app(): App
{
    global $app;
    return $app;
}

/**
 * Dumper
 * @param mixed ...$values
 *
 * @return void
 */
function dd(...$values)
{
    var_dump(...$values);die;
}