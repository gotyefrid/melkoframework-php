<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace src\controllers;

use Gotyefrid\MelkoframeworkCore\Controller;
use Gotyefrid\MelkoframeworkCore\exceptions\NotFoundException;
use Throwable;

class HomeController extends BaseController
{
    public static string $title = 'Главная';

    public function __construct()
    {
        parent::__construct();
        $this->checkAuth();
    }

    /**
     * @return string
     * @throws Throwable
     * @throws NotFoundException
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }
}