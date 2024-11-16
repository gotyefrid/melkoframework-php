<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use Gotyefrid\MelkoframeworkCore\App;
use Gotyefrid\MelkoframeworkCore\ErrorHandler;
use Gotyefrid\MelkoframeworkCore\Request;

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
 * Dumper
 * @param mixed ...$values
 *
 * @return void
 */
function dd(...$values)
{
    var_dump(...$values);die;
}