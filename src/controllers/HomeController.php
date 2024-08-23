<?php

namespace src\controllers;

use core\Auth;
use core\Controller;
use src\models\User;

class HomeController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $auth = new Auth();
        
        if (!$auth->isAuthenticated()) {
            $this->redirect('auth/login');
        }
    }

    public function actionIndex()
    {
        $user = User::findOne();
        return $this->render('index', ['user' => $user]);
    }
}