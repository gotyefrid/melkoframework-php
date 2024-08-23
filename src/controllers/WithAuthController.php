<?php

namespace src\controllers;

use core\Auth;
use core\Controller;

class WithAuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $auth = new Auth();
        
        if (!$auth->isAuthenticated()) {
            $this->redirect('auth/login');
        }
    }
}