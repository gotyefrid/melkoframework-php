<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace src\controllers;

use Gotyefrid\MelkoframeworkCore\App;
use src\models\User;

class MigrationController extends BaseController
{
    public function actionMigrate(): void
    {
        $this->initUserTable();
    }

    public function actionCreateUser(): void
    {
        $this->createUser();
    }

    private function createUser(): void
    {
        $user = new User();
        $user->username = 'user' . rand(1, 999999);
        $user->password = password_hash('admin', PASSWORD_DEFAULT);
        $user->save();
        echo "Юзер создан: Логин $user->username пароль admin <br>";
    }

    private function initUserTable(): void
    {
        $result = App::get()->getPdo()->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users';");

        if ($result->fetch()) {
            echo "Таблица users уже существует <br>";
        } else {
            App::get()->getPdo()->exec("CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY,
                    username TEXT,
                    password TEXT
                )");
            echo "Таблица users создана <br>";
        }
    }
}