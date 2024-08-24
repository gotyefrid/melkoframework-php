<?php

namespace src\controllers;

use core\Controller;
use core\Db;

class MigrationController extends Controller
{
    public function actionMigrate()
    {
        $this->initClicksTable();
        $this->addColumnToClicksTable();
    }

    public function actionResetAdminPassword()
    {
        $this->resetAdminPassword();
    }

    private function initClicksTable()
    {
        Db::getConnection()->exec("CREATE TABLE IF NOT EXISTS clicks (
            id INTEGER PRIMARY KEY,
            created_at TEXT,
            ban_reason TEXT,
            white_showed INTEGER,
            user_agent TEXT,
            url TEXT,
            ip TEXT
        )");
    }

    private function addColumnToClicksTable()
    {
        $db = Db::getConnection();

        // Проверяем, существует ли колонка hideclick_answer в таблице clicks
        $result = $db->query("PRAGMA table_info(clicks)");
        $columnExists = false;

        while ($row = $result->fetch()) {
            if ($row['name'] === 'hideclick_answer') {
                $columnExists = true;
                break;
            }
        }

        // Если колонки не существует, добавляем её
        if (!$columnExists) {
            $db->exec("ALTER TABLE clicks ADD COLUMN hideclick_answer TEXT");
            echo 'Добавили колонку hideclick_answer в таблицу clicks <br>';
            return;
        }

        echo 'Колонка hideclick_answer в таблице clicks уже существует <br>';
    }

    private function resetAdminPassword()
    {
        $defaultPass = password_hash('admin', PASSWORD_DEFAULT);
        Db::getConnection()->exec("UPDATE users SET password = '$defaultPass' WHERE username = 'admin'");
        echo 'Пароль сброшен <br>';
    }
}