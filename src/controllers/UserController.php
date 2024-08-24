<?php

namespace src\controllers;

use core\exceptions\BadRequestException;
use core\exceptions\NotFoundException;
use src\models\User;

class UserController extends WithAuthController
{
    public static $title = 'Пользователи';

    public function actionIndex()
    {
        $users = User::findAll();

        return $this->render('index', ['users' => $users]);
    }

    public function actionCreate()
    {
        $user = new User();

        if ($this->request->isPost()) {
            $user->username = $_POST['username'];
            $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            if ($user->validate() && $user->save()) {
                return $this->redirect('user/index');
            }
        }

        return $this->render('create', ['model' => $user, 'errors' => $user->errors]);
    }

    public function actionUpdate()
    {
        $id = $_GET['id'] ? (int)$_GET['id'] : null;

        if (!$id) {
            throw new BadRequestException('Не передан id');
        }
        
        $user = User::findById($id);

        if (!$user) {
            throw new NotFoundException('Не найден пользователь');
        }

        if ($this->request->isPost()) {
            $user->username = $_POST['username'];
            $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            if ($user->validate() && $user->save()) {
                return $this->redirect('user/index');
            }
        }

        return $this->render('update', ['model' => $user, 'errors' => $user->errors]);
    }

    public function actionDelete()
    {
        $id = $_GET['id'] ? (int)$_GET['id'] : null;

        if (!$id) {
            throw new BadRequestException('Не передан id');
        }

        $user = User::findById($id);

        if (!$user) {
            throw new NotFoundException('Не найден пользователь');
        }

        $user->delete();

        return $this->redirect('user/index');
    }
}