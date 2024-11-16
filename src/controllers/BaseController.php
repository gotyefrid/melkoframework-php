<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace src\controllers;

use Gotyefrid\MelkoframeworkCore\Controller;

class BaseController extends Controller
{
    public function getViewsDir(): string
    {
        return __DIR__ . '/../views' ;
    }

    public function getLayoutsDir(): string
    {
        return __DIR__ . '/../views/layouts' ;
    }
}