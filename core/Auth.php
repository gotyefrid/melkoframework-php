<?php

namespace core;

use src\models\User;
use Exception;

class Auth
{
    /**
     * @var User|null
     */
    protected $user = null;

    /**
     * @var string
     */
    protected $sessionKey = 'user_id';

    public function __construct()
    {
        // Открываем сессию, если она еще не была открыта
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Проверка сессии на наличие авторизованного пользователя
        if (isset($_SESSION[$this->sessionKey])) {
            $this->user = $this->getUserById($_SESSION[$this->sessionKey]);
        }
    }

    /**
     * Логин пользователя
     *
     * @param string $username
     * @param string $password
     * @return bool
     * @throws Exception
     */
    public function login(string $username, string $password): bool
    {
        $user = $this->getUserByUsername($username);

        if ($user && password_verify($password, $user->password)) {
            $this->user = $user;
            $_SESSION[$this->sessionKey] = $user->id;
            return true;
        }

        return false;
    }

    /**
     * Выход пользователя
     *
     * @return void
     */
    public function logout(): void
    {
        unset($_SESSION[$this->sessionKey]);
        $this->user = null;
    }

    /**
     * Проверка, авторизован ли пользователь
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->user !== null;
    }

    /**
     * Получить текущего авторизованного пользователя
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Получить пользователя по ID
     *
     * @param int $id
     * @return User|null
     */
    protected function getUserById(int $id): ?User
    {
        return User::findById($id);
    }

    /**
     * Получить пользователя по имени пользователя
     *
     * @param string $username
     * @return User|null
     */
    protected function getUserByUsername(string $username): ?User
    {
        return User::findByUsername($username);
    }

    /**
     * Зарегистрировать нового пользователя
     *
     * @param string $username
     * @param string $password
     * @return User
     * @throws Exception
     */
    public function register(string $username, string $password): User
    {
        // Реализуйте логику регистрации нового пользователя
        $user = new User();
        $user->username = $username;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->save(); // Сохранение пользователя в базе данных

        return $user;
    }
}
