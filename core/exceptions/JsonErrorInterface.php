<?php

namespace core\exceptions;

interface JsonErrorInterface
{
    public function getErrorJson(): string;
}