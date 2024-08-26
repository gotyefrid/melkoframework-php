<?php

namespace core;

class FlashMessageWidget
{
    /**
     * Отображает flash-сообщения, если они существуют в сессии.
     */
    public static function showFlashIfExist(): string
    {
        self::sessionStart();

        $res = '';

        if (!empty($_SESSION['flash_messages'])) {
            foreach ($_SESSION['flash_messages'] as $flash) {
                $res = '<div class="alert alert-' . htmlspecialchars($flash['type']) . ' alert-dismissible fade show" role="alert">';
                $res .= htmlspecialchars($flash['message']);
                $res .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                $res .= '</div>';
            }

            // После отображения сообщений удаляем их из сессии
            unset($_SESSION['flash_messages']);
        }

        return $res;
    }

    /**
     * Устанавливает flash-сообщение в сессии.
     *
     * @param string $type    Тип сообщения (цветовая схема Bootstrap: primary, danger, success и т.д.).
     * @param string $message Текст сообщения.
     */
    public static function setFlash(string $type, string $message): void
    {
        self::sessionStart();

        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }

        $_SESSION['flash_messages'][] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    private static function sessionStart(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
