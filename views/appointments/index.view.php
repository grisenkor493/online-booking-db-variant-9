<?php require 'views/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Журнал онлайн-записи клиентов</h2>
    <a href="index.php?entity=appointment&action=create" class="btn btn-success">Создать новую запись</a>
</div>

<form method="GET" class="row g-3 mb-4 p-3 bg-light rounded border">
    <input type="hidden" name="entity" value="appointment">
    <input type="hidden" name="action" value="list">
    
    <div class="col-md-4">
        <label class="form-label">Фильтр по дате</label>
        <input type="date" name="date_filter" class="form-control" value="<?= esc($_GET['date_filter'] ?? '') ?>">
    </div>
    
    <div class="col-md-4">
        <label class="form-label">Фильтр по статусу</label>
        <select name="status_filter" class="form-select">
            <option value="">-- Все статусы --</option>
            <option value="waiting" <?= ($_GET['status_filter'] ?? '') === 'waiting' ? 'selected' : '' ?>>Ожидает</option>
            <option value="confirmed" <?= ($_GET['status_filter'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Подтверждена</option>
            <option value="cancelled" <?= ($_GET['status_filter'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Отменена</option>
            <option value="completed" <?= ($_GET['status_filter'] ?? '') === 'completed' ? 'selected' : '' ?>>Завершена</option>
        </select>
    </div>
    
    <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2">Применить фильтры</button>
        <a href="index.php?entity=appointment&action=list" class="btn btn-secondary">Сбросить</a>
    </div>
</form>

<table class="table table-striped table-hover border">
    <thead class="table-dark">
        <tr>
            <th>Дата и время</th>
            <th>Клиент</th>
            <th>Курс</th>
            <th>Статус</th>
            <th>Код</th>
            <th>Действия со статусом</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($appointments)): ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-3">Записи на указанные параметры отсутствуют</td>
            </tr>
        <?php else: ?>
            <?php foreach ($appointments as $app): ?>
                <tr>
                    <td><strong><?= esc($app['appointment_datetime']) ?></strong></td>
                    <td><?= esc($app['client_name']) ?></td>
                    <td><?= esc($app['course_title']) ?></td>
                    <td>
                        <?php if ($app['status'] === 'waiting'): ?>
                            <span class="badge bg-warning text-dark">Ожидает</span>
                        <?php elseif ($app['status'] === 'confirmed'): ?>
                            <span class="badge bg-success">Подтверждена</span>
                        <?php elseif ($app['status'] === 'cancelled'): ?>
                            <span class="badge bg-danger">Отменена</span>
                        <?php elseif ($app['status'] === 'completed'): ?>
                            <span class="badge bg-secondary">Завершена</span>
                        <?php endif; ?>
                    </td>
                    <td><code class="text-uppercase"><?= esc(substr($app['booking_code'], 0, 8)) ?></code></td>
                    <td>
                        <?php if ($app['status'] !== 'cancelled' && $app['status'] !== 'completed'): ?>
                            <a href="index.php?entity=appointment&action=status&id=<?= $app['id'] ?>&status=confirmed" class="btn btn-sm btn-outline-success">Подтвердить</a>
                            <a href="index.php?entity=appointment&action=reschedule&id=<?= $app['id'] ?>" class="btn btn-sm btn-outline-primary">Перенести</a>
                            <a href="index.php?entity=appointment&action=status&id=<?= $app['id'] ?>&status=completed" class="btn btn-sm btn-outline-secondary">Завершить</a>
                            <a href="index.php?entity=appointment&action=status&id=<?= $app['id'] ?>&status=cancelled" class="btn btn-sm btn-outline-danger" onclick="return confirm('Отменить запись?')">Отменить</a>
                        <?php else: ?>
                            <span class="text-muted small">Изменение недоступно</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>