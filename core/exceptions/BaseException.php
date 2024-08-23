<?php

namespace core\exceptions;

abstract class BaseException extends \Exception
{
    protected function getViewPath(): string
    {
        return __DIR__ . '/views';
    }
}