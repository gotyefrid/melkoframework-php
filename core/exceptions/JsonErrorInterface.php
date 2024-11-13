<?php
declare(strict_types=1);

namespace core\exceptions;

interface JsonErrorInterface
{
    public function getErrorJson(): string;
}