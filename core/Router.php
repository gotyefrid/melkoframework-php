<?php

namespace core;

class Router
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var Request
     */
    public $request;

    /**
     * @var string
     */
    public $actionName;

    /**
     * @var string
     */
    public $controllerName;
    public const DEFAULT_ROUTE = 'home/index';

    public function __construct()
    {
        $this->request = new Request();

        $this->actionName = $this->request->getAction();
        $this->controllerName = $this->request->getController();
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        $path = $this->request->getPath();

        if ($path === '/' || $path === '') {
            $this->setDefaultRoute();
            $path = static::DEFAULT_ROUTE;
        }

        $method = $this->request->getMethod();
        
        if ($this->isActionExist()) {
            return $this->callAction();
        }

        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            throw new \DomainException('Route not found', 404);
        }

        return call_user_func($callback);
    }

    protected function isActionExist(): bool
    {
        $controller = $this->getControllerInstance();
        $actionMethod = 'action' . ucfirst($this->actionName);

        return $controller !== null && method_exists($controller, $actionMethod);
    }

    protected function getControllerInstance(): ?Controller
    {
        $controllerClass = $this->getControllerClassName();

        if (!class_exists($controllerClass)) {
            return null;
        }

        return new $controllerClass();
    }

    protected function getControllerClassName(): string
    {
        return 'src\\controllers\\' . ucfirst($this->controllerName) . 'Controller';
    }

    /**
     * @return mixed
     */
    protected function callAction()
    {
        $controller = $this->getControllerInstance();
        $action = 'action' . ucfirst($this->actionName);

        if (!$controller || !method_exists($controller, $action)) {
            throw new \DomainException('Action does not exist', 404);
        }

        return $controller->{$action}();
    }

    private function setDefaultRoute()
    {
        [$controller, $action] = explode('/', static::DEFAULT_ROUTE);
        $this->controllerName = $controller;
        $this->actionName = $action;
    }
}