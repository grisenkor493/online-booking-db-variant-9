<?php require 'views/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Справочник: Клиенты</h2>
    <a href="index.php?entity=client&action=create" class="btn btn-primary">Зарегистрировать клиента</a>
</div>

<form method="GET" action="index.php" class="row g-2 mb-4">
    <input type="hidden" name="entity" value="client">
    <input type="hidden" name="action" value="list">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Поиск по фамилии или телефону..." value="<?= esc($_GET['search'] ?? '') ?>">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-secondary w-100">Найти</button>
    </div>
</form>

<table class="table table-striped table-hover border">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Телефон</th>
            <th>Email</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($clients)): ?>
            <tr>
                <td colspan="6" class="text-center">Записи отсутствуют</td>
            </tr>
        <?php else: ?>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= esc($client['id']) ?></td>
                    <td><?= esc($client['last_name']) ?></td>
                    <td><?= esc($client['first_name']) ?></td>
                    <td><?= esc($client['phone']) ?></td>
                    <td><?= esc($client['email'] ?? '-') ?></td>
                    <td>
                        <a href="index.php?entity=client&action=edit&id=<?= $client['id'] ?>" class="btn btn-sm btn-warning">Ред.</a>
                        <a href="index.php?entity=client&action=delete&id=<?= $client['id'] ?>" class="btn btn-sm btn-danger">Уд.</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>