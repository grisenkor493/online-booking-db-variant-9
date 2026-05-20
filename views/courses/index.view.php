<?php require 'views/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Справочник: Курсы</h2>
    <a href="index.php?entity=course&action=create" class="btn btn-primary">Добавить новый курс</a>
</div>

<form method="GET" action="index.php" class="row g-2 mb-4">
    <input type="hidden" name="entity" value="course">
    <input type="hidden" name="action" value="list">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Поиск курса или уровня..." value="<?= esc($_GET['search'] ?? '') ?>">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-secondary w-100">Найти</button>
    </div>
</form>

<table class="table table-striped table-hover border">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Название курса</th>
            <th>Уровень (Сложность)</th>
            <th>Макс. мест</th>
            <th>Проходной балл теста</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($courses)): ?>
            <tr>
                <td colspan="6" class="text-center">Записи отсутствуют</td>
            </tr>
        <?php else: ?>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= esc($course['id']) ?></td>
                    <td><?= esc($course['title']) ?></td>
                    <td><span class="badge bg-info text-dark"><?= esc($course['level']) ?></span></td>
                    <td><?= esc($course['max_places']) ?></td>
                    <td><?= esc($course['min_score']) ?></td>
                    <td>
                        <a href="index.php?entity=course&action=edit&id=<?= $course['id'] ?>" class="btn btn-sm btn-warning">Ред.</a>
                        <a href="index.php?entity=course&action=delete&id=<?= $course['id'] ?>" class="btn btn-sm btn-danger">Уд.</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>