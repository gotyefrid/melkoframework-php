<?php /** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use core\helpers\GridView;
use core\helpers\Renderer;
use core\helpers\Url;
use src\models\User;

/** @var User[] $users */

$grid = new GridView($users);
$grid->setColumns([
    [
        'attribute' => '{{actions}}',
        'label' => 'Действия',
        'format' => 'raw',
        'value' => static function (User $model) {
            $updateUrl = Url::toRoute(app()->getRequest()->getController() . '/update', ['id' => $model->id]);
            $deleteUrl = Url::toRoute(app()->getRequest()->getController() . '/delete', ['id' => $model->id]);

            return <<<HTML
            <div class="action-buttons">
                <a href="$updateUrl" class="btn btn-warning btn-sm me-2" title="Изменить"
                hx-get="$updateUrl"
                hx-target="#updateUserModal .modal-body"
                hx-select="#createUserForm"
                hx-trigger="click"
                hx-swap="innerHTML ignoreTitle:true"
                data-bs-toggle="modal"
                data-bs-target="#updateUserModal"
                >
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="$deleteUrl" class="btn btn-danger btn-sm" title="Удалить" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                    <i class="bi bi-trash"></i>
                </a>
            </div>
            HTML;

        }
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
$grid->setCurrentPage((int)($_GET['page'] ?? 1));

?>

<div class="container m-2">
    <div>
        <hr>
        <a
                href="<?= Url::toRoute('user/create') ?>"
                class="btn btn-success"
                hx-get="/user/create"
                hx-target="#createUserModal .modal-body"
                hx-select="#createUserForm"
                hx-trigger="click"
                hx-swap="innerHTML ignoreTitle:true"
                data-bs-toggle="modal"
                data-bs-target="#createUserModal"
        >Создать</a>
        <hr>
    </div>
    <?= $grid->render() ?>
</div>

<?= Renderer::render(__DIR__ . '/modal/create.php'); ?>
<?= Renderer::render(__DIR__ . '/modal/update.php'); ?>