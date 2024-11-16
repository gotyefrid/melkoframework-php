<?php
declare(strict_types=1);

namespace Gotyefrid\MelkoframeworkCore;

use Gotyefrid\MelkoframeworkCore\exceptions\NotFoundException;

abstract class AbstractRequest
{
    public string $defaultRoute = 'home/index';
    public string $routeParameterName = 'route';
    public string $controllerNamespace = 'src\\controllers\\';
    protected string $route;

    protected string $controllerId;
    protected string $actionId;

    /**
     * @return mixed
     * @throws NotFoundException
     */
    abstract public function resolve();

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