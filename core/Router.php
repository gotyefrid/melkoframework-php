<?php

namespace core;

use core\exceptions\NotFoundException;

class Router
{
    /**
     * @var array
     */
    protected $routes = [];

    public const DEFAULT_ROUTE = 'statistic/index';

    public function __construct()
    {
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
        $path = Application::$app->request->getPath();

        if ($path === '/' || $path === '') {
            Application::$app->request->setRoute(static::DEFAULT_ROUTE);
        }

        $method = Application::$app->request->getMethod();

        if ($this->isActionExist()) {
            return $this->callAction();
        }

        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            throw new NotFoundException();
        }

        return call_user_func($callback);
    }

    protected function isActionExist(): bool
    {
        $controller = $this->getControllerInstance();
        $actionMethod = 'action' . ucfirst(Application::$app->request->getAction());

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
        return 'src\\controllers\\' . ucfirst(Application::$app->request->getController()) . 'Controller';
    }

    /**
     * @return mixed
     */
    protected function callAction()
    {
        $controller = $this->getControllerInstance();
        $action = 'action' . ucfirst(Application::$app->request->getAction());

        if (!$controller || !method_exists($controller, $action)) {
            throw new NotFoundException('Экшен не найден');
        }

        return $controller->{$action}();
    }
}