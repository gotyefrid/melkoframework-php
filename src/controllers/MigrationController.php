<?php /** @noinspection PhpUnused */

namespace src\controllers;

use core\Application;
use core\Controller;
use src\models\User;

class MigrationController extends Controller
{
    public function actionMigrate(): void
    {
        $this->initUserTable();
        $this->initClicksTable();
    }

    public function actionCreateUser(): void
    {
        $this->createUser();
    }

    public function actionGenerateClicks(): void
    {
        $this->insertRandomClicks();
    }

    private function initClicksTable(): void
    {
        $result = Application::$app->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='clicks';");

        if ($result->fetch()) {
            echo "Таблица clicks уже существует <br>";
        } else {
            Application::$app->db->exec("CREATE TABLE IF NOT EXISTS clicks (
                id INTEGER PRIMARY KEY,
                created_at TEXT,
                ban_reason TEXT,
                white_showed INTEGER,
                user_agent TEXT,
                url TEXT,
                ip TEXT,
                hideclick_answer TEXT
            )");
            echo "Таблица clicks создана <br>";
        }

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
        $result = Application::$app->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users';");

        if ($result->fetch()) {
            echo "Таблица users уже существует <br>";
        } else {
            Application::$app->db->exec("CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY,
                    username TEXT,
                    password TEXT
                )");
            echo "Таблица users создана <br>";
        }
    }

    /**
     * @noinspection PhpSameParameterValueInspection
     */
    private function insertRandomClicks(int $count = 100): void
    {
        $banReasons = ['hideclick', 'Reason1', 'Reason2', 'Reason3', null];
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 11; SM-G998U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Mobile Safari/537.36',
        ];
        $urls = [
            'https://example.com',
            'https://testsite.com',
            'https://randomsite.org',
            'https://mywebsite.net'
        ];

        for ($i = 0; $i < $count; $i++) {
            $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days'));
            $banReason = $banReasons[array_rand($banReasons)];
            $whiteShowed = $banReason === null ? 0 : 1;
            $userAgent = $userAgents[array_rand($userAgents)];
            $url = $urls[array_rand($urls)];
            $ip = long2ip(rand(0, 4294967295)); // Генерация случайного IP-адреса

            $stmt = Application::$app->db->prepare("INSERT INTO clicks (created_at, ban_reason, white_showed, user_agent, url, ip) 
                                    VALUES (:created_at, :ban_reason, :white_showed, :user_agent, :url, :ip)");
            $stmt->bindParam(':created_at', $createdAt);
            $stmt->bindParam(':ban_reason', $banReason);
            $stmt->bindParam(':white_showed', $whiteShowed);
            $stmt->bindParam(':user_agent', $userAgent);
            $stmt->bindParam(':url', $url);
            $stmt->bindParam(':ip', $ip);

            $stmt->execute();
        }
    }
}