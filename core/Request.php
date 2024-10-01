<?php

namespace core;

class Request
{
    private $route;
    public $routeParameterName = 'route';

    public function __construct()
    {
        $this->route = $_GET[$this->routeParameterName] ?? Router::DEFAULT_ROUTE;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getAction()
    {
        return explode('/', $this->getRoute())[1] ?? '';
    }

    public function getController()
    {
        return explode('/', $this->getRoute())[0] ?? '';
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
        $this->route = $path;
    }
}