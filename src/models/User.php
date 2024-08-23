<?php

namespace src\models;

use core\Db;
use core\Model;
use PDO;

class User extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    public $attributes = [
        'id',
        'username',
        'password',
    ];

    public static function tableName(): string
    {
        return 'users';
    }

    public static function findByUsername(string $username)
    {
        return self::findOne(['username' => $username]);
    }

    public function validate(): bool
    {
        if (!$this->password) {
            $this->errors['password'] = 'Необходимо заполнить пароль';
        }

        if ($this->id) {
            $sql = 'SELECT * FROM users WHERE username = :username AND id <> :id LIMIT 1';
            $stmt = Db::getConnection()->prepare($sql);
            $stmt->execute([':username' => $this->username, ':id' => $this->id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results) {
                $this->errors['username'] = 'Такой пользователь уже существует';
            }
        } else {
            if (self::findOne(['username' => $this->username])) {
                $this->errors['username'] = 'Такой пользователь уже существует';
            }
        }

        if (!$this->username) {
            $this->errors['username'] = 'Необходимо заполнить имя пользователя';
        }

        return empty($this->errors);
    }
}