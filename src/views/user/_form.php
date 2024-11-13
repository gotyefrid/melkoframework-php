<?php
declare(strict_types=1);

/** @var array $errors */
/** @var string $route */
/** @var bool $update */
/** @var User $model */

use core\helpers\ArrayHelper;
use src\models\User;

?>
<form id="createUserForm" method="POST" action="<?= $route ?>">
    <div class="mb-3">
        <label for="username" class="form-label">Логин</label>
        <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username"
               name="username" value="<?= $model->username ?>" required>
        <?php if (isset($errors['username'])): ?>
            <div class="invalid-feedback" id="usernameError"><?= $errors['username']; ?></div>
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Пароль</label>
        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password"
               name="password" required>
        <?php if (isset($errors['password'])): ?>
            <div class="invalid-feedback" id="passwordError"><?= $errors['password']; ?></div>
        <?php endif; ?>
    </div>
    <input type="hidden" name="id" value="<?= ArrayHelper::getValue($model, 'id') ?? '' ?>">
    <div id="errorMessages" class="text-danger mb-3">
        <?php if (isset($errors['general'])): ?>
            <?= implode('<br>', $errors['general']); ?>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary w-100"><?= $update ? 'Обновить' : 'Создать пользователя' ?></button>
</form>
<script>
    $(document).ready(function () {
        // Убираем сообщение об ошибке и класс is-invalid при изменении полей ввода
        $('input').on('blur', function () {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').hide();
            $('#errorMessages').html('');
        });
    });
</script>
