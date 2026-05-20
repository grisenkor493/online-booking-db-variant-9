<?php require 'views/layout.php'; ?>

<div class="bg-light p-4 rounded border border-danger mb-4">
    <h2 class="text-danger">⚠️ Отчет: Курсы с очередями из клиентов</h2>
    <p class="text-muted">Ниже выведены курсы, на которые количество успешно сдавших тест клиентов превышает лимит свободных мест (max_places).</p>
</div>

<table class="table table-bordered table-striped table-hover">
    <thead class="table-danger border-dark">
        <tr>
            <th>ID курса</th>
            <th>Название курса</th>
            <th>Уровень</th>
            <th>Лимит мест (max_places)</th>
            <th>Успешно сдали тест</th>
            <th class="table-dark text-white">Длина очереди</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($report)): ?>
            <tr>
                <td colspan="6" class="text-center py-4 text-muted fs-5">🙌 Очередей нет! Мест на курсах хватает всем успешно сдавшим клиентам.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($report as $row): ?>
                <tr>
                    <td><?= esc($row['id']) ?></td>
                    <td><strong><?= esc($row['title']) ?></strong></td>
                    <td><span class="badge bg-info text-dark"><?= esc($row['level']) ?></span></td>
                    <td><?= esc($row['max_places']) ?> мест</td>
                    <td class="text-success font-weight-bold">👤 <?= esc($row['total_passed']) ?> чел.</td>
                    <td class="table-warning text-danger border-dark"><strong>📈 +<?= esc($row['queue_length']) ?> чел. в очереди</strong></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>