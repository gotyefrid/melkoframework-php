<?php
declare(strict_types=1);

namespace Gotyefrid\MelkoframeworkCore\exceptions;

interface JsonErrorInterface
{
    public function getErrorJson(): string;
}