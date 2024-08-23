<?php

use core\helpers\GridView;
use core\helpers\Url;
use src\models\User;

/** @var User[] $users */

$grid = new GridView($users);
$grid->setColumns(['id' => 'ID', 'username' => 'Имя пользователя', 'password' => 'Пароль', '{{actions}}' => 'Действия']);
$grid->setPagination(true, 5); // Включить пагинацию, по 5 элементов на страницу
$grid->setCurrentPage($_GET['page'] ?? 1); // Установить текущую страницу

?>

<div class="container m-2">
    <div>
        <hr>
        <a href="<?= Url::toRoute('user/create') ?>" class="btn btn-success">Создать</a>
        <hr>
    </div>
    <?= $grid->render() ?>
</div>
