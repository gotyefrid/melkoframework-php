<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace src\controllers;

use Gotyefrid\MelkoframeworkCore\Controller;
use Gotyefrid\MelkoframeworkCore\exceptions\BadRequestException;
use Gotyefrid\MelkoframeworkCore\exceptions\NotFoundException;
use Gotyefrid\MelkoframeworkCore\FlashMessageWidget;
use src\models\User;
use Throwable;

class UserController extends BaseController
{
    public static string $title = 'Пользователи';

    public function __construct()
    {
        parent::__construct();
        $this->checkAuth();
    }

    /**
     * @return string
     * @throws NotFoundException
     * @throws Throwable
     */
    public function actionIndex(): string
    {
        $users = User::find('SELECT * FROM users');

        return $this->render('index', ['users' => $users]);
    }

    /**
     * @throws NotFoundException
     * @throws Throwable
     */
    public function actionCreate()
    {
        $this->titlePage = 'Создание пользователя';
        $user = new User();

        if ($this->request->isPost()) {
            $user->username = $_POST['username'];
            $user->password = $_POST['password'];

            if ($user->validate() && $user->save()) {
                FlashMessageWidget::setFlash('success', 'Успешно создано');
                $redirectUrl = 'user/index';

                if ($this->request->isAjax()) {
                    header('HX-Redirect: /' . $redirectUrl);
                    exit();
                }

                return $this->redirect($redirectUrl);
            }
        }


        return $this->render('create', ['model' => $user, 'errors' => $user->errors]);
    }

    /**
     * @return int|string
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws Throwable
     */
    public function actionUpdate()
    {
        $this->titlePage = 'Обновление пользователя';
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
            $user->password = $_POST['password'];

            if ($user->validate() && $user->save()) {
                FlashMessageWidget::setFlash('success', 'Успешно обновлено');
                $redirectUrl = 'user/index';

                if ($this->request->isAjax()) {
                    header('HX-Redirect: /' . $redirectUrl);
                    exit();
                }

                return $this->redirect($redirectUrl);
            }
        }

        return $this->render('update', ['model' => $user, 'errors' => $user->errors]);
    }

    /**
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
            FlashMessageWidget::setFlash('danger', 'Не найден пользователь');
            return $this->redirect('user/index');
        }

        $user->delete();
        FlashMessageWidget::setFlash('success', 'Успешно удалено');

        return $this->redirect('user/index');
    }
}