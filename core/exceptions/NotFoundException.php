<?php
declare(strict_types=1);

namespace core\exceptions;

use core\helpers\Renderer;
use core\helpers\Url;
use Throwable;

class NotFoundException extends BaseException implements HttpErrorInterface
{
    public function __construct($message = "Страница не найдена", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function getErrorHtml(): string
    {
        $homeUrl = Url::toHome();

        $content = Renderer::render($this->getViewPath() . '/notFoundHttpException/view.php', [
            'homeUrl' => $homeUrl,
            'exception' => $this
        ]);

        return Renderer::render($this->getViewPath() . '/layouts/default.php', [
            'content' => $content,
            'title' => $this->message
        ]);
    }
}
