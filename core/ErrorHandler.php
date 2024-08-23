<?php

namespace core;

use core\exceptions\HttpErrorInterface;
use core\exceptions\JsonErrorInterface;
use Throwable;

class ErrorHandler
{
    /**
     * @var Throwable
     */
    public $exception;

    public function __construct(Throwable $throwable)
    {
        $this->exception = $throwable;
    }

    public function handle()
    {
        if ($this->exception instanceof HttpErrorInterface) {
            return $this->exception->getErrorHtml();
        }

        if ($this->exception instanceof JsonErrorInterface) {
            return $this->exception->getErrorJson();
        }

        $text = 'Вызвано исключение: ' . get_class($this->exception);
        $text .= '<br>' . $this->exception->getMessage();

        return $text;
    }
}
