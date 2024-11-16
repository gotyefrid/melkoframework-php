<?php /** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);
/** @var array $errors */

/** @var User $model */

use Gotyefrid\MelkoframeworkCore\helpers\Renderer;
use Gotyefrid\MelkoframeworkCore\helpers\Url;
use melkoframework\models\User;

?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h3 class="text-center mb-4">Редактировать пользователя</h3>
            <?= Renderer::render(__DIR__ . '/_form.php', [
                'errors' => $errors,
                'model' => $model,
                'update' => true,
                'route' => Url::toRoute('user/update', ['id' => $model->id]),
            ]) ?>
        </div>
    </div>
</div>
