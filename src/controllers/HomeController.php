<?php

namespace src\controllers;

class HomeController extends WithAuthController
{
    public static $title = 'Главная';

    public function actionIndex()
    {
        return $this->render('index');
    }
}