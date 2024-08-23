<?php
/** @var string $homeUrl */
/** @var BaseException $exception */

use core\exceptions\BaseException;

?>
<h1><?= $exception->getCode() ?></h1>
<h3><?= $exception->getMessage() ?></h3>
<p><a href="<?= $homeUrl ?>">Вернуться на главную страницу</a></p>