<?php require 'views/layout.php'; ?>

<div class="card border-danger mt-4" style="max-width: 500px;">
    <div class="card-header bg-danger text-white">Удаление клиента</div>
    <div class="card-body">
        <p>Вы действительно хотите удалить клиента <strong><?= esc($client['last_name']) ?> <?= esc($client['first_name']) ?></strong>?</p>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <button type="submit" class="btn btn-danger">Удалить</button>
            <a href="index.php?entity=client&action=list" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div>

</body>
</html>