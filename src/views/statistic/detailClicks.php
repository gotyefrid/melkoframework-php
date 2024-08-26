<?php

use core\helpers\GridView;
use core\helpers\Url;
use src\models\Click;

/** @var Click[] $clicks */
/** @var string $fromDate */
/** @var string $toDate */
/** @var string $page */

$grid = new GridView($clicks);
$grid->setColumns([
    [
        'attribute' => 'id',
    ],
    [
        'attribute' => 'created_at',
        'value' => function (Click $model) {
            $dateTime = new DateTime($model->created_at);
            $dateTime->setTimezone(new DateTimeZone('+3'));
            return $dateTime->format('Y-m-d H:i:s');
        }
    ],
    [
        'attribute' => 'ban_reason',
    ],
    [
        'attribute' => 'white_showed',
    ],
    [
        'attribute' => 'user_agent',
    ],
    [
        'attribute' => 'url',
    ],
    [
        'attribute' => 'ip',
    ],
    [
        'attribute' => 'hideclick_answer',
    ],
]);
$grid->setPagination(true, 20);
$grid->setCurrentPage($page);

?>

<form class="mb-4" method="GET">
    <div class="row mb-4">
        <div class="col-auto">
            <a href="<?= Url::toRoute(Url::currentRoute(), [
                'from_date' => '2000-01-01',
                'to_date' => '2999-01-01',
            ]) ?>" class="btn btn-danger">За
                всё время</a>
        </div>
        <div class="col-auto">
            <a href="<?= Url::toRoute(Url::currentRoute()) ?>" class="btn btn-secondary">Сегодня</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label for="from_date" class="form-label">От</label>
            <input type="date" id="from_date" name="from_date" class="form-control"
                   value="<?= $fromDate; ?>">
        </div>
        <div class="col-md-4">
            <label for="to_date" class="form-label">До</label>
            <input type="date" id="to_date" name="to_date" class="form-control" value="<?= $toDate; ?>">
            <input type="hidden" name="path" value="<?= Url::currentRoute() ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Применить</button>
        </div>
    </div>
</form>
<div class="container m-2">
    <?= $grid->render() ?>
</div>