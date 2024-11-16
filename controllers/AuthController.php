<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace melkoframework\controllers;

use Gotyefrid\MelkoframeworkCore\exceptions\NotFoundException;
use melkoframework\models\User;
use melkoframework\services\AuthService;
use Throwable;

class AuthController extends BaseController
{
    private AuthService $auth;

    public function __construct()
    {
        parent::__construct();

        $this->auth = new AuthService();
    }

    /**
     * @return int|string
     * @throws Throwable
     * @throws NotFoundException
     */
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
                    return $this->redirect($_GET['redirect'], [], true);
                }

                return $this->redirect('home/index');
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
            $this->redirect($redirect, [], true);
        }

        if ($referrer) {
            $this->redirect($referrer, [], true);
        }

        $this->redirect('/');
    }
}