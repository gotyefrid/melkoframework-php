<?php

namespace core;

use core\exceptions\NotFoundException;

class Router
{
    /**
     * @var array
     */
    protected $routes = [];

    public const DEFAULT_ROUTE = 'home/index';

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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
     * @throws NotFoundException
     */
    public function resolve()
    {
        $route = $this->request->getRoute();

        if ($route === '/' || $route === '') {
            $this->request->setRoute(static::DEFAULT_ROUTE);
        }

        $method = $this->request->getMethod();

        if ($this->isActionExist()) {
            return $this->callAction();
        }

        $callback = $this->routes[$method][$route] ?? false;

        if ($callback === false) {
            throw new NotFoundException();
        }

        return call_user_func($callback);
    }

    protected function isActionExist(): bool
    {
        $controller = $this->getControllerInstance();
        $actionMethod = 'action' . ucfirst($this->request->getAction());

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
        return 'src\\controllers\\' . ucfirst($this->request->getController()) . 'Controller';
    }

    /**
     * @return mixed
     * @throws NotFoundException
     */
    protected function callAction()
    {
        $controller = $this->getControllerInstance();
        $action = 'action' . ucfirst($this->request->getAction());

        if (!$controller || !method_exists($controller, $action)) {
            throw new NotFoundException('Экшен не найден');
        }

        return $controller->{$action}();
    }
}