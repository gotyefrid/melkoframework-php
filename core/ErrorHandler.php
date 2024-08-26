<?php

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

        $text = 'Вызвано исключение: ' . get_class($throwable);
        $text .= '<br>' . $throwable->getMessage();

        return $text;
    }
}
