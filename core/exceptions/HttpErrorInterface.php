<?php
declare(strict_types=1);

namespace core\exceptions;

interface HttpErrorInterface
{
    public function getErrorHtml(): string;
}