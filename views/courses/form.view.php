<?php require 'views/layout.php'; ?>

<h2><?= isset($data['id']) ? 'Редактировать параметры курса' : 'Добавить новый языковой курс' ?></h2>

<form method="POST" class="mt-4" style="max-width: 600px;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label class="form-label">Название курса</label>
        <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= esc($data['title']) ?>" placeholder="например: Английский для начинающих">
        <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= $errors['title'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Уровень курса (A1, A2, B1...)</label>
        <input type="text" name="level" class="form-control <?= isset($errors['level']) ? 'is-invalid' : '' ?>" value="<?= esc($data['level']) ?>" placeholder="например: B1">
        <?php if (isset($errors['level'])): ?><div class="invalid-feedback"><?= $errors['level'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Максимальное количество мест</label>
        <input type="number" name="max_places" class="form-control <?= isset($errors['max_places']) ? 'is-invalid' : '' ?>" value="<?= esc($data['max_places']) ?>">
        <?php if (isset($errors['max_places'])): ?><div class="invalid-feedback"><?= $errors['max_places'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Минимальный балл входного теста (для зачисления)</label>
        <input type="number" name="min_score" class="form-control <?= isset($errors['min_score']) ? 'is-invalid' : '' ?>" value="<?= esc($data['min_score']) ?>">
        <?php if (isset($errors['min_score'])): ?><div class="invalid-feedback"><?= $errors['min_score'] ?></div><?php endif; ?>
    </div>

    <button type="submit" class="btn btn-success">Сохранить курс</button>
    <a href="index.php?entity=course&action=list" class="btn btn-light">Отмена</a>
</form>

</body>
</html>