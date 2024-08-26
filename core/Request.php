<?php

namespace core;

class Request
{
    private $path;

    public function __construct()
    {
        $this->path = isset($_GET['path']) ? $_GET['path'] : Router::DEFAULT_ROUTE;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getAction()
    {
        return explode('/', $this->getPath())[1] ?? '';
    }

    public function getController()
    {
        return explode('/', $this->getPath())[0] ?? '';
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public function setRoute(string $path)
    {
        $this->path = $path;
    }
}