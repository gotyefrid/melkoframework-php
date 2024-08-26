<?php /** @noinspection PhpUnused */

namespace src\controllers;

use core\Controller;
use core\exceptions\BadRequestException;
use core\exceptions\NotFoundException;
use src\models\User;

class UserController extends Controller
{
    public static $title = 'Пользователи';

    public function __construct()
    {
        parent::__construct();
        $this->checkAuth();
    }

    /**
     * @return string
     * @throws NotFoundException
     * @throws \Throwable
     */
    public function actionIndex(): string
    {
        $users = User::find('SELECT * FROM users');

        return $this->render('index', ['users' => $users]);
    }

    /**
     * @throws NotFoundException
     * @throws \Throwable
     */
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

    /**
     * @return int|string
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws \Throwable
     */
    public function actionUpdate()
    {
        $id = $_GET['id'] ? (int)$_GET['id'] : null;

        if (!$id) {
            throw new BadRequestException('Не передан id');
        }

        /** @var User $user */
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

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function actionDelete(): int
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