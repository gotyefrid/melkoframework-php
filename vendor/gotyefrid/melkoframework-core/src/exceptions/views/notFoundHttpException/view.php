<?php
declare(strict_types=1);

/** @var string $homeUrl */
/** @var BaseException $exception */

use Gotyefrid\MelkoframeworkCore\exceptions\BaseException;

?>
<h1><?= $exception->getCode() ?></h1>
<h3><?= $exception->getMessage() ?></h3>
<p><a href="<?= $homeUrl ?>">Вернуться на главную страницу</a></p>