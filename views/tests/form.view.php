<?php require 'views/layout.php'; ?>

<h2>Внести результат входного тестирования</h2>

<form method="POST" class="mt-4" style="max-width: 600px;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label class="form-label">Выберите клиента</label>
        <select name="client_id" class="form-select <?= isset($errors['client_id']) ? 'is-invalid' : '' ?>">
            <option value="">-- Выберите из списка --</option>
            <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>"><?= esc($c['last_name']) ?> <?= esc($c['first_name']) ?> (ID: <?= $c['id'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['client_id'])): ?><div class="invalid-feedback"><?= $errors['client_id'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Курс, на который претендует</label>
        <select name="course_id" class="form-select <?= isset($errors['course_id']) ? 'is-invalid' : '' ?>">
            <option value="">-- Выберите из списка --</option>
            <?php foreach ($courses as $co): ?>
                <option value="<?= $co['id'] ?>"><?= esc($co['title']) ?> [Уровень: <?= esc($co['level']) ?>] (Мин. балл: <?= $co['min_score'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['course_id'])): ?><div class="invalid-feedback"><?= $errors['course_id'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Полученный балл</label>
        <input type="number" name="score" class="form-control <?= isset($errors['score']) ? 'is-invalid' : '' ?>" value="<?= esc($data['score']) ?>">
        <?php if (isset($errors['score'])): ?><div class="invalid-feedback"><?= $errors['score'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Дата проведения</label>
        <input type="date" name="test_date" class="form-control <?= isset($errors['test_date']) ? 'is-invalid' : '' ?>" value="<?= esc($data['test_date']) ?>">
        <?php if (isset($errors['test_date'])): ?><div class="invalid-feedback"><?= $errors['test_date'] ?></div><?php endif; ?>
    </div>

    <button type="submit" class="btn btn-success">Сохранить результат</button>
    <a href="index.php?entity=test&action=list" class="btn btn-light">Отмена</a>
</form>

</body>
</html>