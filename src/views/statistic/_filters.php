<?php

use core\helpers\Url;

/** @var string $fromDate */
/** @var string $toDate */
?>

<form class="mb-4" method="GET">
    <input type="hidden" name="path" value="<?= Url::currentRoute() ?>">
    <div class="row mb-4">
        <div class="col-auto">
            <a href="<?= Url::toRoute(Url::currentRoute(), [
                'from_date' => '2000-01-01',
                'to_date' => '2999-01-01',
            ]) ?>" class="btn btn-danger">За всё время</a>
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
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Применить</button>
        </div>
    </div>
</form>
