<?php

use core\helpers\GridView;
use core\helpers\Renderer;
use src\models\Click;

/** @var Click[] $clicks */
/** @var string $fromDate */
/** @var string $toDate */
/** @var string $page */

$grid = new GridView($clicks);
$grid->setColumns([
    [
        'attribute' => 'id',
        'label' => 'ID'
    ],
    [
        'attribute' => 'created_at',
        'value' => function (Click $model) {
            $dateTime = new DateTime($model->created_at);
            $dateTime->setTimezone(new DateTimeZone('+3'));
            return $dateTime->format('Y-m-d H:i:s');
        },
        'label' => 'Время клика (МСК)'
    ],
    [
        'attribute' => 'ban_reason',
        'label' => 'Причина бана'
    ],
    [
        'attribute' => 'white_showed',
        'label' => 'Показан вайт',
        'value' => function (Click $model) {
            return $model->white_showed ? 'Да' : 'Нет';
        }
    ],
    [
        'attribute' => 'user_agent',
        'label' => 'User Agent'
    ],
    [
        'attribute' => 'url',
        'label' => 'URL'
    ],
    [
        'attribute' => 'ip',
        'label' => 'IP'
    ],
    [
        'attribute' => 'hideclick_answer',
        'label' => 'Ответ hideclick'
    ],
]);
$grid->setPagination(true, 20);
$grid->setCurrentPage($page);

?>

<?= Renderer::render(__DIR__ . '/_filters.php', [
        'fromDate' => $fromDate,
        'toDate' => $toDate,
]) ?>
<div class="container m-2">
    <?= $grid->render() ?>
</div>