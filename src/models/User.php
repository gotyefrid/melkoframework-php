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
}