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

use core\helpers\Renderer;
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

    <?= Renderer::render(__DIR__ . '/_filters.php', [
        'fromDate' => $fromDate,
        'toDate' => $toDate,
    ]) ?>

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