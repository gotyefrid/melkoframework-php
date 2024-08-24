<?php

namespace src\controllers;

use core\Auth;
use core\Controller;
use src\models\User;

class AuthController extends Controller
{
    /**
     * @var Auth
     */
    private $auth;

    public function __construct()
    {
        parent::__construct();

        $this->auth = new Auth();
    }

    public function actionLogin()
    {
        $this->layout = 'login';
        $errors = [];
        $user = new User();

        if ($this->request->isPost()) {
            $username = $_POST['username'] ?? null;
            $password = $_POST['password'] ?? null;
            $user->username = $username;

            if (!User::findByUsername($username)) {
                $errors['general'] = ['Пользователь не найден'];
                return $this->render('login', ['errors' => $errors, 'model' => $user]);
            }

            if ($this->auth->login($username, $password)) {
                if (isset($_GET['redirect'])) {
                    return $this->redirect($_GET['redirect'], true);
                }

                return $this->redirect('statistic/index');
            } else {
                $errors['general'] = ['Неверный логин или пароль'];
            }
        }

        return $this->render('login', ['errors' => $errors, 'model' => $user]);
    }

    public function actionLogout()
    {
        $this->auth->logout();

        $referrer = $_SERVER['HTTP_REFERER'] ?? null;
        $redirect = $_GET['redirect'] ?? null;

        if ($redirect) {
            $this->redirect($redirect, true);
        }

        if ($referrer) {
            $this->redirect($referrer, true);
        }

        $this->redirect('/');
    }
}