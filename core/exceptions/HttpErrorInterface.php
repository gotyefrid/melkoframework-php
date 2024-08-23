<?php

namespace core\exceptions;

interface HttpErrorInterface
{
    public function getErrorHtml(): string;
}