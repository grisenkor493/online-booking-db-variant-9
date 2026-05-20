<?php require 'views/layout.php'; ?>

<div class="card border-danger mt-4" style="max-width: 500px;">
    <div class="card-header bg-danger text-white">Удаление языкового курса</div>
    <div class="card-body">
        <p>Вы действительно хотите удалить курс <strong><?= esc($course['title']) ?></strong> (Уровень: <?= esc($course['level']) ?>)?</p>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <button type="submit" class="btn btn-danger">Удалить</button>
            <a href="index.php?entity=course&action=list" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div>

</body>
</html>