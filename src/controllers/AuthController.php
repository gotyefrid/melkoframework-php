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
        if ($this->auth->login('admin', 'admin')) {
            if (isset($_GET['redirect'])) {
                $this->redirect($_GET['redirect']);
            }

            $this->redirect('home/index');
        }
    }

    public function actionLogout()
    {
        $this->auth->logout();

        $referrer = $_SERVER['HTTP_REFERER'] ?? null;

        if ($referrer) {
            $this->redirect($referrer, true);
        }

        var_dump(1234);die;
        $this->redirect('home/test');
    }
}