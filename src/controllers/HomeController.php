<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace src\controllers;

use core\Controller;
use core\exceptions\NotFoundException;
use Throwable;

class HomeController extends Controller
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