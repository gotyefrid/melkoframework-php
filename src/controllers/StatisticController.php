<?php

namespace src\controllers;

use core\Application;
use core\Controller;
use core\Db;
use src\models\Click;
use src\models\ClickRepository;

class StatisticController extends Controller
{
    public static $title = 'Статистика';
    public static $hideClickPath;

    public function __construct()
    {
        parent::__construct();
        $this::$hideClickPath = Application::$appPath . '/../index.php';
    }

    public function actionIndex()
    {
        $fromDate = ($_GET['from_date'] ?? date('Y-m-d')) ?: '2000-01-01';
        $toDate = ($_GET['to_date'] ?? date('Y-m-d')) ?: '2999-01-01';
        $isCloEnabled = $this->isCloEnabled();
// Получение статистики
        $totalClicks = ClickRepository::getTotalClicks($fromDate, $toDate);
        $hideClickCount = ClickRepository::getHideClickCount($fromDate, $toDate);
        $customCloakCount = ClickRepository::getCustomCloakCount($fromDate, $toDate);
        $goesToBlack = ClickRepository::getGoesToBlackCount($fromDate, $toDate);

        $customCloakReasons = ClickRepository::getCustomCloakReasons($fromDate, $toDate);
        $customCloakReasonsMap = array_reduce($customCloakReasons, function ($carry, $item) {
            $carry[$item['ban_reason']] = $item['count'];
            return $carry;
        }, []);

        return $this->render('index', [
            'isCloEnabled' => $isCloEnabled,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'totalClicks' => $totalClicks,
            'hideClickCount' => $hideClickCount,
            'customCloakCount' => $customCloakCount,
            'goesToBlack' => $goesToBlack,
            'customCloakReasons' => $customCloakReasons,
            'customCloakReasonsMap' => $customCloakReasonsMap,
        ]);
    }

    public function actionDetailClicks()
    {
        $filter = $_GET['type'] ?? '';
        $fromDate = ($_GET['from_date'] ?? date('Y-m-d')) ?: '2000-01-01';
        $toDate = ($_GET['to_date'] ?? date('Y-m-d')) ?: '2999-01-01';

        $createdAtWhere = "created_at > '$fromDate 00:00:00' AND created_at < '$toDate 23:59:59'";

        switch ($filter) {
            case 'hideClickCount':
                $clicks = Click::findAll("WHERE ban_reason = 'hideclick' AND $createdAtWhere");
                break;
            case 'customCloakCount':
                $clicks = Click::findAll("WHERE ban_reason IS NOT NULL AND ban_reason != 'hideclick' AND $createdAtWhere");
                break;
            case 'goesToBlack':
                $clicks = Click::findAll("WHERE white_showed = 0 AND $createdAtWhere");
                break;
            default:
                $clicks = Click::findAll($fromDate || $toDate ? "WHERE $createdAtWhere" : []);
        }

        return $this->render('detailClicks', [
            'clicks' => $clicks,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
    }

    public function actionChangeStatusClo()
    {
        $status = $_POST['cloak_status'] ?? '0';
        $cloakStatus = $status ? 'on' : 'off';
        $fileContents = file_get_contents(self::$hideClickPath);
        $pattern = "/\\\$statusClo = '.*?';/";

        $replacement = "\$statusClo = '$cloakStatus';";
        $newContents = preg_replace($pattern, $replacement, $fileContents);

        file_put_contents(self::$hideClickPath, $newContents);
    }

    private function isCloEnabled(): bool
    {
        $fileContents = file_get_contents(self::$hideClickPath);

        return strpos($fileContents, '$statusClo = \'on\';') !== false;
    }

    public function insertRandomClicks(int $count = 100): void
    {
        $banReasons = ['hideclick', 'Reason1', 'Reason2', 'Reason3', null];
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 11; SM-G998U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Mobile Safari/537.36',
        ];
        $urls = [
            'https://example.com',
            'https://testsite.com',
            'https://randomsite.org',
            'https://mywebsite.net'
        ];

        for ($i = 0; $i < $count; $i++) {
            $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days'));
            $banReason = $banReasons[array_rand($banReasons)];
            $whiteShowed = $banReason === null ? 0 : 1;
            $userAgent = $userAgents[array_rand($userAgents)];
            $url = $urls[array_rand($urls)];
            $ip = long2ip(rand(0, 4294967295)); // Генерация случайного IP-адреса

            $stmt = Db::getConnection()->prepare("INSERT INTO clicks (created_at, ban_reason, white_showed, user_agent, url, ip) 
                                    VALUES (:created_at, :ban_reason, :white_showed, :user_agent, :url, :ip)");
            $stmt->bindParam(':created_at', $createdAt);
            $stmt->bindParam(':ban_reason', $banReason);
            $stmt->bindParam(':white_showed', $whiteShowed);
            $stmt->bindParam(':user_agent', $userAgent);
            $stmt->bindParam(':url', $url);
            $stmt->bindParam(':ip', $ip);

            $stmt->execute();
        }
    }
}