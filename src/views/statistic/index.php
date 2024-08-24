<?php

/** @var bool $isCloEnabled */
/** @var string $fromDate */
/** @var string $toDate */
/** @var string $totalClicks */
/** @var string $hideClickCount */
/** @var string $customCloakCount */
/** @var string $goesToBlack */
/** @var array $customCloakReasons */
/** @var array $customCloakReasonsMap */

/** @var User $user */

use core\helpers\Url;
use src\models\User;

?>
<div class="container mt-5">

    <div class="form-check form-switch mb-4">
        <input class="form-check-input" <?= $isCloEnabled ? 'checked' : '' ?> type="checkbox"
               id="cloakStatusSwitch" name="cloak_status">
        <label class="form-check-label" for="cloakStatusSwitch">Статус кло (<span
                    id="cloStatusSpan"><?= $isCloEnabled ? 'Включена' : 'Выключена' ?></span>)</label>
    </div>

    <h1>Статистика</h1>

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

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Всего кликов</th>
            <th>Отрезал hideclick</th>
            <th>Отрезал CustomCloak</th>
            <th>Попали на Блэк</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <a href="<?= Url::toRoute('statistic/detailClicks', ['from_date' => $fromDate, 'to_date' => $toDate]) ?>"><?= $totalClicks; ?></a>
            </td>
            <td>
                <a href="<?= Url::toRoute('statistic/detailClicks', ['from_date' => $fromDate, 'to_date' => $toDate, 'type' => 'hideClickCount']) ?>"><?= $hideClickCount; ?></a>
            </td>
            <td>
                    <span
                            data-bs-toggle="tooltip"
                            data-bs-html="true"
                            data-bs-title="<?= implode('<br>', array_map(
                                function ($reason, $count) {
                                    if ($count === "") {
                                        return htmlspecialchars($reason);
                                    }

                                    return htmlspecialchars($reason) . ': ' . htmlspecialchars($count);
                                },
                                array_keys($customCloakReasonsMap),
                                $customCloakReasonsMap
                            ));
                            ?>">
                            <a href="<?= Url::toRoute('statistic/detailClicks', ['from_date' => $fromDate, 'to_date' => $toDate, 'type' => 'customCloakCount']) ?>"><?= $customCloakCount; ?></a>
                    </span>
            </td>
            <td>
                <a href="<?= Url::toRoute('statistic/detailClicks', ['from_date' => $fromDate, 'to_date' => $toDate, 'type' => 'goesToBlack']) ?>"><?= $goesToBlack; ?></a>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        changeCloStatusListener();

        function changeCloStatusListener() {
            const cloakStatusSwitch = document.getElementById('cloakStatusSwitch');

            cloakStatusSwitch.addEventListener('change', function () {
                // Получаем состояние переключателя (вкл/выкл)
                const isChecked = cloakStatusSwitch.checked;
                const spanSwitch = document.getElementById('cloStatusSpan');

                if (isChecked) {
                    spanSwitch.innerHTML = 'Включена';
                } else {
                    spanSwitch.innerHTML = 'Выключена';
                }

                const formData = new FormData();
                formData.append('cloak_status', isChecked ? '1' : '0');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= Url::toRoute('statistic/changeStatusClo') ?>', true);
                xhr.send(formData);
            });
        }
    });
</script>