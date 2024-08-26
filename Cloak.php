<?php /** @noinspection PhpUnused */

use core\Application;

require __DIR__ . '/vendor/autoload.php';

final class Cloak
{
    public $url = '';
    public $userAgent = '';

    /**
     * @var string|null Название метода по которому проверка не прошла
     */
    private $bannedBy = null;

    public function __construct(?string $url = null, ?string $userAgent = null)
    {
        $this->url = $url ?? $this->getCurrentUrl();
        $this->userAgent = $userAgent ?? $this->getCurrentUserAgent();
    }

    public function doCloak(): bool
    {
        if (!$this->userAgent) {
            $this->bannedBy = 'user-agent not found';
            return false;
        }

        $results = [
            'isAllowedBrowser' => $this->isAllowedBrowser(),
            'isParametersExists' => $this->isParametersExists(['gbraid', 'gclid', 'wbraid']),
            'isRightUserAgent' => $this->isRightUserAgent(),
        ];

        foreach ($results as $name => $result) {
            if ($result !== true) {
                $this->bannedBy = $name;

                return false;
            }
        }

        return true;
    }

    public function isParametersExists(array $params): bool
    {
        $parsedUrl = parse_url($this->url);

        if (!isset($parsedUrl['query'])) {
            return false;
        }

        parse_str($parsedUrl['query'], $queryParams);

        $result = false;

        foreach ($params as $param) {
            if (isset($queryParams[$param])) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    public function isRightUserAgent(): bool
    {
        if ($this->isSamsungBrowser()) {
            return true;
        }

        $isAndroid = $this->isAndroidOs();
        $isChrome = $this->isChromeBrowserForAndroid();

        // Если это андройд и хром - то проверяем чтобы не было модели
        if ($isAndroid && $isChrome) {
            return $this->isRightAndroidUserAgent();
        }

        $isIphoneOs = $this->isIphoneOs();
        $isSafari = $this->isSafariBrowser();
        $isChromeMobile = $this->isChromeBrowserForIphone();
        $isRightBrowser = $isSafari || $isChromeMobile;

        // Если это iPhone и один из браузеров (хром или сафари) - то проверяем чтобы скобки начинались с iPhone
        if ($isIphoneOs && $isRightBrowser) {
            return $this->isRightIphoneUserAgent();
        }

        return false;
    }

    public function isRightAndroidUserAgent(): bool
    {
        // Регулярное выражение для поиска содержимого первых скобок
        $pattern = '/\(([^)]+)\)/';

        // Проверяем наличие содержимого первых скобок
        if (preg_match($pattern, $this->userAgent, $matches)) {
            // Получаем содержимое первых скобок
            $content = $matches[1];

            // Проверяем, заканчивается ли содержимое на большую букву K
            if (!$this->endsWith($content, '; K')) {
                return false;
            }
        }

        return true;
    }

    public function isIphoneOs(): bool
    {
        return stripos($this->userAgent, 'CPU iPhone OS') !== false;
    }

    public function isRightIphoneUserAgent(): bool
    {
        // Регулярное выражение для поиска содержимого первых скобок
        $pattern = '/\(([^)]+)\)/';

        // Проверяем наличие содержимого первых скобок
        if (preg_match($pattern, $this->userAgent, $matches)) {
            // Получаем содержимое первых скобок
            $content = $matches[1];

            // Проверяем, начинается ли содержимое с нужной строки
            if (!$this->startsWith($content, 'iPhone')) {
                return false;
            }
        }

        return true;
    }

    public function isSafariBrowser(): bool
    {
        return stripos($this->userAgent, '(KHTML, like Gecko) Version/') !== false;
    }

    public function isAndroidOs(): bool
    {
        return stripos($this->userAgent, 'Android') !== false;
    }

    public function isSamsungBrowser(): bool
    {
        return stripos($this->userAgent, '(KHTML, like Gecko) SamsungBrowser') !== false;
    }

    public function isChromeBrowser(): bool
    {
        return $this->isChromeBrowserForAndroid() || $this->isChromeBrowserForIphone();
    }

    public function isChromeBrowserForAndroid(): bool
    {
        return stripos($this->userAgent, '(KHTML, like Gecko) Chrome') !== false;
    }

    public function isChromeBrowserForIphone(): bool
    {
        return stripos($this->userAgent, '(KHTML, like Gecko) CriOS') !== false;
    }

    public function isMobileBrowser(): bool
    {
        return stripos($this->userAgent, 'Mobile') !== false;
    }

    public function isAllowedBrowser(): bool
    {
        if (!$this->isMobileBrowser()) {
            return false;
        }

        if (
            $this->isChromeBrowser() ||
            $this->isSamsungBrowser() ||
            $this->isSafariBrowser()
        ) {
            return true;
        }

        return false;
    }

    private function getCurrentUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
            ? "https://"
            : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];

        return $protocol . $host . $uri;
    }

    private function getCurrentUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /** @noinspection PhpSameParameterValueInspection */
    private function endsWith(string $string, string $substring): bool
    {
        // Проверяем длину подстроки
        $length = strlen($substring);

        // Если длина подстроки больше длины строки, сразу возвращаем false
        if ($length === 0) {
            return true; // Пустая подстрока считается совпадающей
        }

        // Используем substr для проверки окончания строки
        return substr($string, -$length) === $substring;
    }

    /** @noinspection PhpSameParameterValueInspection */
    private function startsWith(string $string, string $substring): bool
    {
        // Проверяем длину подстроки
        $length = strlen($substring);

        // Если длина подстроки больше длины строки, сразу возвращаем false
        if ($length === 0) {
            return true; // Пустая подстрока считается совпадающей
        }

        // Используем substr для проверки начала строки
        return substr($string, 0, $length) === $substring;
    }

    public function logClick(bool $whiteShowed = false, string $hideclickAnswer = ''): void
    {
        $createdAt = date('Y-m-d H:i:s');

        $banReason = null;

        if ($whiteShowed) {
            $banReason = $this->bannedBy ?? 'hideclick';
        }

        $userAgent = $this->userAgent;
        $url = $this->url;
        $ip = $this->getCurrentIp();
        $whiteShowed = (int)$whiteShowed;
        $stmt = Application::$app->db->prepare("
                INSERT INTO clicks (created_at, ban_reason, white_showed, user_agent, url, ip, hideclick_answer) 
                VALUES (:created_at, :ban_reason, :white_showed, :user_agent, :url, :ip, :hideclick_answer)");
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':ban_reason', $banReason);
        $stmt->bindParam(':white_showed', $whiteShowed);
        $stmt->bindParam(':user_agent', $userAgent);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':hideclick_answer', $hideclickAnswer);

        $stmt->execute();
    }

    private function getCurrentIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}