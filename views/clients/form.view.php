<?php require 'views/layout.php'; ?>

<h2><?= isset($data['id']) ? 'Редактировать профиль клиента' : 'Регистрация нового клиента' ?></h2>

<form method="POST" class="mt-4" style="max-width: 600px;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label class="form-label">Фамилия</label>
        <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" value="<?= esc($data['last_name']) ?>">
        <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= $errors['last_name'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Имя</label>
        <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" value="<?= esc($data['first_name']) ?>">
        <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= $errors['first_name'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Телефон</label>
        <input type="text" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" value="<?= esc($data['phone']) ?>">
        <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= $errors['phone'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= esc($data['email']) ?>">
    </div>

    <button type="submit" class="btn btn-success">Сохранить</button>
    <a href="index.php?entity=client&action=list" class="btn btn-light">Отмена</a>
</form>

</body>
</html>