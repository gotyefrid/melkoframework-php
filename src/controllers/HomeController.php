<?php

namespace src\controllers;

use src\models\User;

class HomeController extends WithAuthController
{
    public $title = 'Главная';

    public function actionIndex()
    {
        $user = User::findOne();
        return $this->render('index', ['user' => $user]);
    }
}