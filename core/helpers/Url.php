<?php

namespace core\helpers;

use core\Application;

class Url
{
    public static function getDomain(bool $withProtocol = true): string
    {
        $host = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

        // Определяем протокол
        if ($withProtocol) {
            $domain = $protocol . '://' . $host;
        } else {
            $domain = $host;
        }

        // Добавляем порт, если он не является стандартным
        $port = $_SERVER['SERVER_PORT'];

        if (($protocol === 'http' && $port != 80) || ($protocol === 'https' && $port != 443)) {
            $domain .= ':' . $port;
        }

        return $domain;
    }

    public static function getCurrentUrl(bool $withQuery = true): string
    {
        $url = self::getDomain() . $_SERVER['REQUEST_URI'];

        if ($withQuery === false) {
            $urlComponents = parse_url($url);
            $url = $urlComponents['scheme'] . '://' . $urlComponents['host'] . $urlComponents['path'];
        }

        return $url;
    }

    public static function toRoute(string $path, array $params = []): string
    {
        $params = array_merge([Application::$app->request->routeParameterName => $path], $params);

        $baseUrl = self::getCurrentUrl(false);

        return $baseUrl . '?' . http_build_query($params);
    }

    public static function toHome(): string
    {
        return self::toRoute('/');
    }

    public static function currentRoute(): string
    {
        return $_GET[Application::$app->request->routeParameterName] ?? Application::$app->router::DEFAULT_ROUTE;
    }

    public static function currentAction(): string
    {
        return isset($_GET[Application::$app->request->routeParameterName]) ? (explode('/', $_GET[Application::$app->request->routeParameterName])[1] ?? '') : '';
    }

    public static function currentController(): string
    {
        return isset($_GET[Application::$app->request->routeParameterName]) ? (explode('/', $_GET[Application::$app->request->routeParameterName])[0] ?? '') : '';
    }
}