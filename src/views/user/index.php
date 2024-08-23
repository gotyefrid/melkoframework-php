<?php

use core\helpers\GridView;
use src\models\User;

/** @var User[] $users */

$grid = new GridView($users);
$grid->setColumns(['id' => 'ID', 'username' => 'Имя пользователя', 'password' => 'Пароль', '{{actions}}' => 'Действия']);
$grid->setPagination(true, 1); // Включить пагинацию, по 5 элементов на страницу
$grid->setCurrentPage($_GET['page'] ?? 1); // Установить текущую страницу

?>

<div class="panel">
    <?= $grid->render() ?>
</div>
