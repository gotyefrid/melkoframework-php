<?php /** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use core\helpers\GridView;
use core\helpers\Url;
use src\models\User;

/** @var User[] $users */

$grid = new GridView($users);
$grid->setColumns([
    [
        'attribute' => '{{actions}}',
        'label' => 'Действия',
    ],
    [
        'attribute' => 'id',
        'label' => 'ID',
    ],
    [
        'attribute' => 'username',
        'label' => 'Имя пользователя'
    ],
    [
        'attribute' => 'password',
        'label' => 'Пароль',
        'value' => function () {
            return '***';
        }
    ],
]);
$grid->setPagination(true, 5);
$grid->setCurrentPage($_GET['page'] ?? 1);

?>

<div class="container m-2">
    <div>
        <hr>
        <a href="<?= Url::toRoute('user/create') ?>" class="btn btn-success">Создать</a>
        <hr>
    </div>
    <?= $grid->render() ?>
</div>
