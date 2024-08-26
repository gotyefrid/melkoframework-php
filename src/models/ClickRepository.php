<?php

namespace src\models;

use core\Application;

class ClickRepository
{
    /**
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpSameParameterValueInspection
     */
    private static function convertToUTC(string $dateTime, string $timezone = 'Europe/Moscow'): string
    {
        $date = new \DateTime($dateTime, new \DateTimeZone($timezone));
        $date->setTimezone(new \DateTimeZone('UTC'));
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpSameParameterValueInspection
     */
    private static function convertFromUTC(string $dateTime, string $timezone = 'Europe/Moscow'): string
    {
        $date = new \DateTime($dateTime, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }

    public static function getTotalClicks(string $fromDate, string $toDate): int
    {
        $db = Application::$app->db;
        $fromDateUTC = self::convertToUTC($fromDate . ' 00:00:00');
        $toDateUTC = self::convertToUTC($toDate . ' 23:59:59');

        $stmt = $db->prepare("SELECT COUNT(*) FROM clicks WHERE created_at BETWEEN :from_date AND :to_date");
        $stmt->bindParam(':from_date', $fromDateUTC);
        $stmt->bindParam(':to_date', $toDateUTC);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function getHideClickCount(string $fromDate, string $toDate): int
    {
        $db = Application::$app->db;
        $fromDateUTC = self::convertToUTC($fromDate . ' 00:00:00');
        $toDateUTC = self::convertToUTC($toDate . ' 23:59:59');

        $stmt = $db->prepare("SELECT COUNT(*) FROM clicks WHERE ban_reason = 'hideclick' AND created_at BETWEEN :from_date AND :to_date");
        $stmt->bindParam(':from_date', $fromDateUTC);
        $stmt->bindParam(':to_date', $toDateUTC);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function getCustomCloakCount(string $fromDate, string $toDate): int
    {
        $db = Application::$app->db;
        $fromDateUTC = self::convertToUTC($fromDate . ' 00:00:00');
        $toDateUTC = self::convertToUTC($toDate . ' 23:59:59');

        $stmt = $db->prepare("SELECT COUNT(*) FROM clicks WHERE ban_reason IS NOT NULL AND ban_reason != 'hideclick' AND created_at BETWEEN :from_date AND :to_date");
        $stmt->bindParam(':from_date', $fromDateUTC);
        $stmt->bindParam(':to_date', $toDateUTC);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function getGoesToBlackCount(string $fromDate, string $toDate): int
    {
        $db = Application::$app->db;
        $fromDateUTC = self::convertToUTC($fromDate . ' 00:00:00');
        $toDateUTC = self::convertToUTC($toDate . ' 23:59:59');

        $stmt = $db->prepare("SELECT COUNT(*) FROM clicks WHERE white_showed = 0 AND created_at BETWEEN :from_date AND :to_date");
        $stmt->bindParam(':from_date', $fromDateUTC);
        $stmt->bindParam(':to_date', $toDateUTC);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function getCustomCloakReasons(string $fromDate, string $toDate): array
    {
        $db = Application::$app->db;
        $fromDateUTC = self::convertToUTC($fromDate . ' 00:00:00');
        $toDateUTC = self::convertToUTC($toDate . ' 23:59:59');

        $stmt = $db->prepare("
            SELECT ban_reason, COUNT(*) as count 
            FROM clicks 
            WHERE ban_reason IS NOT NULL AND ban_reason != 'hideclick' 
            AND created_at BETWEEN :from_date AND :to_date 
            GROUP BY ban_reason
        ");
        $stmt->bindParam(':from_date', $fromDateUTC);
        $stmt->bindParam(':to_date', $toDateUTC);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $data[] = [
            'ban_reason' => '______',
            'count' => ''
        ];

        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM clicks 
            WHERE user_agent LIKE :user_agent_part 
            AND created_at BETWEEN :from_date AND :to_date 
        ");
        $part = '%Instagram%';
        $stmt->bindParam(':user_agent_part', $part);
        $stmt->bindParam(':from_date', $fromDateUTC);
        $stmt->bindParam(':to_date', $toDateUTC);
        $stmt->execute();
        $data[] = [
            'ban_reason' => 'Instagram',
            'count' => (int)$stmt->fetchColumn()
        ];

        $part = '%GSA/%';
        $stmt->bindParam(':user_agent_part', $part);
        $stmt->execute();
        $data[] = [
            'ban_reason' => 'Google Search',
            'count' => (int)$stmt->fetchColumn()
        ];

        $part = '%[FB_%';
        $stmt->bindParam(':user_agent_part', $part);
        $stmt->execute();
        $data[] = [
            'ban_reason' => 'Facebook',
            'count' => (int)$stmt->fetchColumn()
        ];

        return $data;
    }
}