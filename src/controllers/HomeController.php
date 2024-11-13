<?php
declare(strict_types=1);

namespace src\controllers;

use core\Controller;

class HomeController extends Controller
{
    public static string $title = 'Главная';

    public function __construct()
    {
        parent::__construct();
        // $this->checkAuth();
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}