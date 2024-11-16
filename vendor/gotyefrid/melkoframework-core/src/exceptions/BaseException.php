<?php
declare(strict_types=1);

namespace Gotyefrid\MelkoframeworkCore\exceptions;

use Exception;

abstract class BaseException extends Exception
{
    protected function getViewPath(): string
    {
        return __DIR__ . '/views';
    }
}