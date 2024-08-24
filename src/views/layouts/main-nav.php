<?php

use core\helpers\Url;
use src\controllers\HomeController;
use src\controllers\StatisticController;
use src\controllers\UserController;

$items = [
    [
        'url' => Url::toRoute('home/index'),
        'active' => Url::currentRoute() === 'home/index',
        'label' => HomeController::$title,
    ],
    [
        'url' => Url::toRoute('statistic/index'),
        'active' => Url::currentRoute() === 'statistic/index',
        'label' => StatisticController::$title,
    ],
    [
        'url' => Url::toRoute('statistic/detailClicks'),
        'active' => Url::currentRoute() === 'statistic/detailClicks',
        'label' => 'Клики',
    ],
    [
        'url' => Url::toRoute('user/index'),
        'active' => Url::currentRoute() === 'user/index',
        'label' => UserController::$title,
    ],
    [
        'url' => Url::toRoute('auth/logout'),
        'active' => Url::currentRoute() === 'auth/logout',
        'label' => 'Выйти',
    ],
];

?>
<header class="d-flex justify-content-center py-3">
    <ul class="nav nav-pills">
        <?php foreach ($items as $item): ?>
            <li class="nav-item">
                <a class="nav-link <?= $item['active'] ? 'active' : '' ?>"
                   href="<?= $item['url'] ?>"><?= $item['label'] ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</header>
