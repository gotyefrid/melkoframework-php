<?php

namespace src\models;

use core\Model;

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

        if (!$this->username) {
            $this->errors['username'] = 'Необходимо заполнить имя пользователя';
        }

        return empty($this->errors);
    }
}