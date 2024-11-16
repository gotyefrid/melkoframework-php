<?php
declare(strict_types=1);

namespace core;

use core\exceptions\NotFoundException;

class Request extends AbstractRequest
{
    /**
     * @return mixed
     * @throws NotFoundException
     */
    public function resolve()
    {
        $this->route = $this->parseRoute();
        $routeKeys = explode('/', $this->route);
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
}