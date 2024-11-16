<?php
declare(strict_types=1);

namespace Gotyefrid\MelkoframeworkCore\exceptions;

use Gotyefrid\MelkoframeworkCore\helpers\Renderer;
use Gotyefrid\MelkoframeworkCore\helpers\Url;
use Throwable;

class BadRequestException extends BaseException implements HttpErrorInterface
{
    public function __construct($message = "Неверный запрос", $code = 400, Throwable $previous = null)
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

        $content = Renderer::render($this->getViewPath() . '/badRequestException/view.php', [
            'homeUrl' => $homeUrl,
            'exception' => $this
        ]);

        return Renderer::render($this->getViewPath() . '/layouts/default.php', [
            'content' => $content,
            'title' => $this->message
        ]);
    }
}
