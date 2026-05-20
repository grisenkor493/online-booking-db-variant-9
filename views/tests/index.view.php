<?php require 'views/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Входное тестирование клиентов</h2>
    <a href="index.php?entity=test&action=create" class="btn btn-primary">Внести результат теста</a>
</div>

<table class="table table-striped table-hover border">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Клиент</th>
            <th>Целевой курс</th>
            <th>Набранный балл</th>
            <th>Статус</th>
            <th>Дата теста</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($results)): ?>
            <tr>
                <td colspan="7" class="text-center">Записи отсутствуют</td>
            </tr>
        <?php else: ?>
            <?php foreach ($results as $res): ?>
                <?php $isPassed = $res['score'] >= $res['min_score']; ?>
                <tr>
                    <td><?= esc($res['id']) ?></td>
                    <td><?= esc($res['client_name']) ?></td>
                    <td><?= esc($res['course_title']) ?></td>
                    <td><strong><?= esc($res['score']) ?></strong> <small class="text-muted">(надо >= <?= $res['min_score'] ?>)</small></td>
                    <td>
                        <?php if ($isPassed): ?>
                            <span class="badge bg-success">Успешно сдан</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Не сдан</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($res['test_date']) ?></td>
                    <td>
                        <a href="index.php?entity=test&action=delete&id=<?= $res['id'] ?>" class="btn btn-sm btn-danger">Уд.</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>