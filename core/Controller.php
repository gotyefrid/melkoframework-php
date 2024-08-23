<?php

namespace core;

class Controller
{
    public $layout = 'main';

    /**
     * @var Request|null
     */
    public $request = null;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function renderPhpFile(string $absoluteFilePath, array $params = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($params);

        try {
            require $absoluteFilePath;
            return ob_get_clean();
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }

    public function render(string $view, array $params = [])
    {
        $path = __DIR__ . '/../src/views/' . $this->request->getController() . '/' . $view . '.php';

        if (!file_exists($path)) {
            throw new \Exception("View file ($path) not found");
        }

        $content = $this->renderPhpFile($path, $params);

        $layoutPath = __DIR__ . '/../src/views/layouts/' . $this->layout . '.php';

        return $this->renderPhpFile($layoutPath, ['content' => $content]);
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