<?php
declare(strict_types=1);

namespace core\helpers;


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

        $isNonStandardPort = ($protocol === 'http' && $port != 80) || ($protocol === 'https' && $port != 443);
        $hostHasNoPort = strpos($host, ':') === false;

        if ($isNonStandardPort && $hostHasNoPort) {
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
        if (app()->isGetParamRouter) {
            $params = array_merge([app()->getRequest()->routeParameterName => $path], $params);

            return '/' . '?' . http_build_query($params);
        }

        return '/' . $path . ($params ? '?' . http_build_query($params) : '');
    }

    public static function toHome(): string
    {
        return self::toRoute('/');
    }
}