<?php
declare(strict_types=1);

namespace core\helpers;

use Throwable;

class Renderer
{
    /**
     * @param string $absoluteFilePath
     * @param array $params variables to be passed to the view file
     *
     * @return string
     */
    public static function render(string $absoluteFilePath, array $params = []): string
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();

        if (PHP_VERSION_ID >= 80000) {
            // Для PHP 8.0 и выше передаем false
            /** @noinspection PhpStrictTypeCheckingInspection */
            ob_implicit_flush(false);
        } else {
            // Для более старых версий передаем 0
            ob_implicit_flush(0);
        }

        extract($params);

        try {
            require $absoluteFilePath;
            return ob_get_clean();
        } catch (Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }
}