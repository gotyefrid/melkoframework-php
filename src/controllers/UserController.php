<?php

namespace src\controllers;

use src\models\User;

class UserController extends WithAuthController
{
    public $title = 'Пользователи';

    public function actionIndex()
    {
        $users = User::findAll();

        return $this->render('index', ['users' => $users]);
    }
}