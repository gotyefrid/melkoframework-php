<?php
/** @var array $errors */
/** @var User $model */

use src\models\User;

?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h3 class="text-center mb-4">Вход</h3>
            <form id="loginForm" method="POST" action="?path=auth/login">
                <div class="mb-3">
                    <label for="username" class="form-label">Логин</label>
                    <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?= $model->username ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <div class="invalid-feedback" id="usernameError"><?php echo $errors['username']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password"  required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback" id="passwordError"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>
                <div id="errorMessages" class="text-danger mb-3">
                    <?php if (isset($errors['general'])): ?>
                        <?php echo implode('<br>', $errors['general']); ?>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Убираем сообщение об ошибке и класс is-invalid при изменении полей ввода
        $('input').on('blur', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').hide();
            $('#errorMessages').html('');
        });
    });
</script>
