<?php
declare(strict_types=1);

namespace core;

use core\exceptions\NotFoundException;
use core\helpers\Renderer;
use Throwable;

abstract class Controller
{
    public static string $title = 'Заголовок';

    public string $layout = 'main';

    public Request $request;

    public function __construct()
    {
        $this->request = App::$app->getRequest();
    }

    /**
     * @throws NotFoundException
     * @throws Throwable
     */
    public function render(string $view, array $params = []): string
    {
        $path = __DIR__ . '/../src/views/' . $this->request->getController() . '/' . $view . '.php';

        if (!file_exists($path)) {
            $showPath = '../views/' . $this->request->getController() . '/' . $view . '.php';
            throw new NotFoundException("Файл вида ($showPath) не найден");
        }

        $content = Renderer::render($path, $params);

        $layoutPath = __DIR__ . '/../src/views/layouts/' . $this->layout . '.php';

        return Renderer::render($layoutPath, ['content' => $content, 'title' => $this::$title]);
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
        if (App::$app->isGetParamRouter) {
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

    public function checkAuth(): void
    {
        $auth = new Auth();

        if (!$auth->isAuthenticated()) {
            $this->redirect('auth/login');
        }
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