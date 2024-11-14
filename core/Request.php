<?php
declare(strict_types=1);

namespace core;

use core\exceptions\NotFoundException;

class Request
{
    public string $defaultRoute = 'home/index';
    public string $routeParameterName = 'route';
    public string $controllerNamespace = 'src\\controllers\\';
    private string $route;

    private string $controllerId;
    private string $actionId;

    public function __construct()
    {
        $this->route = $this->parseRoute();
    }

    /**
     * @return mixed
     * @throws NotFoundException
     */
    public function resolve()
    {
        $route = $this->getRoute();
        $routeKeys = explode('/', $route);
        $this->controllerId = $routeKeys[0];
        $this->actionId = $routeKeys[1] ?? '';

        if (!$this->actionId) {
            $this->actionId = 'index';
        }

        $controllerInstance = $this->getControllerInstance($this->controllerId);

        if (!$controllerInstance) {
            throw new NotFoundException('Не найден такой контроллер');
        }

        return $controllerInstance->callAction($this->actionId);
    }

    private function getControllerInstance(string $controllerName): ?Controller
    {
        $controllerClass = $this->controllerNamespace . ucfirst($controllerName) . 'Controller';

        if (!class_exists($controllerClass)) {
            return null;
        }

        return new $controllerClass();
    }

    /**
     * @return string
     */
    private function parseRoute(): string
    {
        $route = empty($_GET[$this->routeParameterName]) ? null : $_GET[$this->routeParameterName];

        if (!$route) {
            $path = $_SERVER['REQUEST_URI'];

            if (strpos($path, '?') !== null) {
                $path = explode('?', $path)[0];
            }

            if ($path === '/') {
                return $this->defaultRoute;
            }

            // Удаляем префиксный "/"
            $route = ltrim($path, '/');
        }

        return $route;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getAction(): string
    {
        return $this->actionId;
    }

    public function getController(): string
    {
        return $this->controllerId;
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isPost(): bool
    {
        return strtolower($this->getMethod()) == strtolower('POST');
    }

    public function isAjax(): bool
    {
        // Проверяем, является ли запрос от htmx
        if (isset($_SERVER['HTTP_HX_REQUEST']) && $_SERVER['HTTP_HX_REQUEST'] === 'true') {
            return true;
        }

        // Проверяем, является ли запрос обычным AJAX-запросом
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            return true;
        }

        // Если ни одно условие не выполнено, значит это не AJAX/htmx запрос
        return false;
    }
}