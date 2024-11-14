<?php
declare(strict_types=1);

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
    public string $username = '';

    /**
     * @var string
     */
    public string $password = '';

    public array $attributes = [
        'id',
        'username',
        'password',
    ];

    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * @param string $username
     *
     * @return User|null
     */
    public static function findByUsername(string $username): ?User
    {
        return static::findByCondition('username', $username)[0] ?? null;
    }

    public function validate(): bool
    {
        if (!$this->password) {
            $this->errors['password'] = 'Необходимо заполнить пароль';
        }

        if ($this->id) {
            // Редактирование
            $exists = static::find('SELECT * FROM users WHERE username = :name AND id != :id', [
                'name' => $this->username,
                'id' => $this->id
            ]);

            if ($exists) {
                $this->errors['username'] = 'Такой пользователь уже существует';
            }
        } else {
            if (self::findByUsername($this->username)) {
                $this->errors['username'] = 'Такой пользователь уже существует';
            }
        }

        if (!$this->username) {
            $this->errors['username'] = 'Необходимо заполнить имя пользователя';
        }

        if ($this->password) {
            if (strlen($this->password) < 3) {
                $this->errors['password'] = 'Минимум 3 символа';
            }
        }

        return empty($this->errors);
    }

    public function save(bool $runValidation = true): bool
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        return parent::save($runValidation);
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return User[]
     */
    public static function find(string $sql, array $params = []): array
    {
        return parent::find($sql, $params);
    }
}