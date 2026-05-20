<?php require 'views/layout.php'; ?>

<h2>Оформление записи на курс / тестирование</h2>

<?php if (isset($errors['global'])): ?>
    <div class="alert alert-danger"><?= esc($errors['global']) ?></div>
<?php endif; ?>

<form method="POST" class="mt-4" style="max-width: 600px;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label class="form-label">Клиент *</label>
        <select name="client_id" class="form-select <?= isset($errors['client_id']) ? 'is-invalid' : '' ?>">
            <option value="">-- Выберите клиента --</option>
            <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>"><?= esc($c['last_name']) ?> <?= esc($c['first_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['client_id'])): ?><div class="invalid-feedback"><?= $errors['client_id'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Языковой курс *</label>
        <select name="course_id" class="form-select <?= isset($errors['course_id']) ? 'is-invalid' : '' ?>">
            <option value="">-- Выберите курс --</option>
            <?php foreach ($courses as $co): ?>
                <option value="<?= $co['id'] ?>"><?= esc($co['title']) ?> (<?= esc($co['level']) ?>)</option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['course_id'])): ?><div class="invalid-feedback"><?= $errors['course_id'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Дата записи *</label>
        <input type="date" id="appointment_date" name="appointment_date" min="<?= date('Y-m-d') ?>" class="form-control <?= isset($errors['appointment_date']) ? 'is-invalid' : '' ?>">
        <?php if (isset($errors['appointment_date'])): ?><div class="invalid-feedback"><?= $errors['appointment_date'] ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label d-block">Доступное время *</label>
        <div id="slots_container" class="p-3 border rounded bg-light text-muted">
            Выберите дату, чтобы увидеть свободные временные слоты.
        </div>
        <?php if (isset($errors['appointment_time'])): ?><div class="text-danger small mt-1"><?= $errors['appointment_time'] ?></div><?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Подтвердить запись</button>
    <a href="index.php?entity=appointment&action=list" class="btn btn-light">Отмена</a>
</form>

<script>
document.getElementById('appointment_date').addEventListener('change', function() {
    const date = this.value;
    const container = document.getElementById('slots_container');
    
    if(!date) return;
    
    container.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div> Загрузка слотов...';
    
    fetch('get_slots.php?date=' + date)
        .then(response => response.json())
        .then(slots => {
            container.innerHTML = '';
            if(slots.length === 0) {
                container.innerHTML = '<span class="text-danger">Нет свободного времени на выбранную дату.</span>';
                return;
            }
            
            slots.forEach(time => {
                container.innerHTML += `
                    <div class="form-check form-check-inline m-1">
                        <input class="form-check-input text-dark" type="radio" name="appointment_time" id="t_${time}" value="${time}" required>
                        <label class="form-check-label badge bg-dark p-2 fs-6" for="t_${time}">${time}</label>
                    </div>
                `;
            });
        })
        .catch(() => {
            container.innerHTML = '<span class="text-danger">Ошибка загрузки временных слотов.</span>';
        });
});
</script>

</body>
</html>