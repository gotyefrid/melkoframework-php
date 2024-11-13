<?php
declare(strict_types=1);

/** @var string $content */
/** @var string $title */

use core\FlashMessageWidget;

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/src/views/layouts/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/src/views/layouts/css/bootstrap-icons.css">
    <title><?= $title ?? 'Заголовок' ?></title>
    <script src="/src/views/layouts/js/jquery-3.7.1.js"></script>
</head>
<body>
<?php require('main-nav.php') ?>
<div class="container mt-3">
    <?= FlashMessageWidget::showFlashIfExist() ?>
    <?= $content ?>
</div>
<script src="/src/views/layouts/js/bootstrap.bundle.js"></script>
</body>
</html>
