<?php

namespace core;

use PDO;

class Db
{
    public static $dbPath = __DIR__ . '/../databases/database.db';

    public static function initDataBase()
    {
        if (!file_exists(self::$dbPath)) {
            self::initUserTable();
            self::initClicksTable();
        }
    }

    public static function getConnection(): PDO
    {
        return new PDO('sqlite:' . self::$dbPath);
    }

    public static function find(string $sql, array $params = []): array
    {
        $db = self::getConnection();
        $stmt = $db->prepare($sql);

        foreach ($params as $key => $param) {
            $stmt->bindParam($key, $param);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findOne(string $sql, array $params = []): array
    {
        return self::find($sql, $params)[0] ?? [];
    }

    private static function initUserTable()
    {
        $adminName = 'admin';
        $adminPass = 'admin';

        self::getConnection()->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY,
                username TEXT,
                password TEXT
            )");

        $passHash = password_hash($adminPass, PASSWORD_DEFAULT);
        $adminUser = self::findOne("SELECT * FROM users WHERE username = '$adminName'");

        if (count($adminUser) === 0) {
            // create admin user
            $query = self::getConnection()->prepare('INSERT INTO users (username, password) VALUES (:username, :pass)');
            $query->bindParam(':username', $adminName);
            $query->bindParam(':pass', $passHash);
            $query->execute();
        }
    }

    private static function initClicksTable()
    {
        self::getConnection()->exec("CREATE TABLE IF NOT EXISTS clicks (
            id INTEGER PRIMARY KEY,
            created_at TEXT,
            ban_reason TEXT,
            white_showed INTEGER,
            user_agent TEXT,
            url TEXT,
            ip TEXT,
            hideclick_answer TEXT
        )");
    }
}