<?php
declare(strict_types=1);

use Gotyefrid\MelkoframeworkCore\App;
use Gotyefrid\MelkoframeworkCore\helpers\Url;
use melkoframework\controllers\HomeController;
use melkoframework\controllers\UserController;

$items = [
    [
        'url' => Url::toRoute('home/index'),
        'active' => App::get()->getRequest()->getRoute() === 'home/index',
        'label' => HomeController::$title,
    ],
    [
        'url' => Url::toRoute('user/index'),
        'active' => App::get()->getRequest()->getRoute() === 'user/index',
        'label' => UserController::$title,
    ],
    [
        'url' => Url::toRoute('auth/logout'),
        'active' => App::get()->getRequest()->getRoute() === 'auth/logout',
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
