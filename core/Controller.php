<?php

namespace core;

use core\exceptions\NotFoundException;
use core\helpers\Renderer;

abstract class Controller
{
    public static $title = 'Заголовок';

    public $layout = 'main';

    /**
     * @var Request|null
     */
    public $request = null;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function render(string $view, array $params = [])
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
     * @param string $path
     * @param bool $absolute
     *
     * @return int
     */
    public function redirect(string $path, bool $absolute = false): int
    {
        if ($absolute) {
            header('Location: ' . $path);
            exit();
        }

        header('Location: ' . '?path=' . $path);
        exit();
    }
}