<?php

namespace src\controllers;

use core\Controller;

class HomeController extends Controller
{
    public static $title = 'Главная';

    public function __construct()
    {
        parent::__construct();
        $this->checkAuth();
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}