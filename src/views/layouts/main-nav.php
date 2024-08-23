<?php

use core\helpers\Url;

?>
<style>
    .nav-link {
        color: rgb(255 255 255 / 80%)!important;
    }
</style>
<nav class="navbar navbar-expand-md bg-primary" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Переключение навигации">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::toRoute('home/index') ?>">Главная</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::toRoute('user/index') ?>">Пользователи</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::toRoute('auth/logout') ?>">Выйти</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
