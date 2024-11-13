<?php
declare(strict_types=1);

namespace core;

use core\exceptions\HttpErrorInterface;
use core\exceptions\JsonErrorInterface;
use Throwable;

class ErrorHandler
{
    public function handle(Throwable $throwable)
    {
        if ($throwable instanceof HttpErrorInterface) {
            return $throwable->getErrorHtml();
        }

        if ($throwable instanceof JsonErrorInterface) {
            return $throwable->getErrorJson();
        }

        // Проверка, если это localhost или IP в диапазоне 192.168.*
        if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || preg_match('/^192\.168\./', $_SERVER['REMOTE_ADDR'])) {
            throw $throwable; // выбрасываем исключение в случае отладки на локалке
        }

        $text = 'Вызвано исключение: ' . get_class($throwable);
        $text .= '<br>' . $throwable->getMessage();

        return $text;
    }
}
