<?php

namespace core\helpers;

class Renderer
{
    public static function render(string $absoluteFilePath, array $params = []): string
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
}