<?php
declare(strict_types=1);

namespace Gotyefrid\MelkoframeworkCore;

use Gotyefrid\MelkoframeworkCore\exceptions\NotFoundException;
use Gotyefrid\MelkoframeworkCore\helpers\Renderer;
use Throwable;

abstract class Controller
{
    public static string $title = 'Заголовок';

    /** @var string|false */
    public $layout = 'main';
    public string $titlePage = '';

    public AbstractRequest $request;

    public function __construct()
    {
        $this->request = App::get()->getRequest();
    }

    abstract function getViewsDir(): string;
    abstract function getLayoutsDir(): string;

    /**
     * @throws NotFoundException
     * @throws Throwable
     */
    public function render(string $view, array $params = []): string
    {
        $sep = DIRECTORY_SEPARATOR;
        $path = implode($sep, [
            rtrim($this->getViewsDir(), $sep),
            $this->request->getController(),
            $view . '.php'
        ]);

        if (!file_exists($path)) {
            throw new NotFoundException("View File ($path) not found");
        }

        $content = Renderer::render($path, $params);

        if ($this->layout === false) {
            return $content;
        }

        $layoutPath = implode($sep, [
            $this->getLayoutsDir(),
            $this->layout . '.php'
        ]);

        if (!file_exists($layoutPath)) {
            throw new NotFoundException("Layout File ($layoutPath) not found");
        }

        return Renderer::render($layoutPath, ['content' => $content, 'title' => $this->titlePage ?: static::$title]);
    }


    /**
     * @throws NotFoundException
     * @throws Throwable
     */
    public function renderPartial(string $view, array $params = []): string
    {
        $sep = DIRECTORY_SEPARATOR;
        $path = implode($sep, [
            rtrim($this->getViewsDir(), $sep),
            $this->request->getController(),
            $view . '.php'
        ]);

        if (!file_exists($path)) {
            throw new NotFoundException("Файл вида ($path) не найден");
        }

        return Renderer::render($path, $params);
    }

    /**
     * @param string $route
     * @param array $params
     * @param bool $absolute
     *
     * @return int
     */
    public function redirect(string $route, array $params = [], bool $absolute = false): int
    {
        // Если это абсолютный URL, редиректим сразу
        if ($absolute) {
            $url = $route . (!empty($params) ? '?' . http_build_query($params) : '');
            header('Location: ' . $url);
            exit();
        }

        // Если роутинг через GET-параметр, добавляем параметр маршрута
        if (App::get()->isGetParamRouter) {
            $params = array_merge([$this->request->routeParameterName => $route], $params);
            $url = '/?' . http_build_query($params);
        } else {
            // Относительный URL с GET-параметрами, если они есть
            $url = '/' . ltrim($route, '/') . (!empty($params) ? '?' . http_build_query($params) : '');
        }

        // Выполняем редирект и завершаем выполнение
        header('Location: ' . $url);
        exit();
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    public function isActionExist(string $action): bool
    {
        return method_exists($this, 'action' . ucfirst($action));
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws NotFoundException
     */
    public function callAction(string $action)
    {
        if ($this->isActionExist($action)) {
            return $this->{'action' . ucfirst($action)}();
        }

        throw new NotFoundException('Не найден такой экшен');
    }
}