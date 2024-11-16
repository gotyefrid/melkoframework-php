<?php
declare(strict_types=1);

namespace Gotyefrid\MelkoframeworkCore\exceptions;

interface HttpErrorInterface
{
    public function getErrorHtml(): string;
}