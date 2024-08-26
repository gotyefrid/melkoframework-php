<?php /** @noinspection PhpUnused */

namespace src\controllers;

use core\Application;
use core\Controller;
use core\exceptions\NotFoundException;
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
        $this->checkAuth();
    }

    /**
     * @return string
     * @throws NotFoundException
     * @throws \Throwable
     */
    public function actionIndex(): string
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

    /**
     * @return string
     * @throws NotFoundException
     * @throws \Throwable
     */
    public function actionDetailClicks(): string
    {
        $filter = $_GET['type'] ?? '';
        $fromDate = ($_GET['from_date'] ?? date('Y-m-d')) ?: '2000-01-01';
        $toDate = ($_GET['to_date'] ?? date('Y-m-d')) ?: '2999-01-01';

        $conditions = [];

        switch ($filter) {
            case 'hideClickCount':
                $conditions[] = "ban_reason = 'hideclick'";
                break;
            case 'customCloakCount':
                $conditions[] = "ban_reason IS NOT NULL AND ban_reason != 'hideclick'";
                break;
            case 'goesToBlack':
                $conditions[] = "white_showed = 0";
                break;
            default:
                break;
        }

        $conditions[] = "created_at > :fromDate AND created_at < :toDate";
        $sql = "SELECT * FROM clicks WHERE " . implode(' AND ', $conditions) . " ORDER BY created_at DESC";

        $clicks = Click::find($sql, [
            ':fromDate' => $fromDate . ' 00:00:00',
            ':toDate' => $toDate . ' 23:59:59',
        ]);

        return $this->render('detailClicks', [
            'clicks' => $clicks,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'page' => $_GET['page'] ?? 1,
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
}