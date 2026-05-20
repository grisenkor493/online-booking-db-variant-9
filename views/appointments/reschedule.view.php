<?php require 'views/layout.php'; ?>

<h2>Перенос времени записи</h2>

<?php if (isset($errors['global'])): ?>
    <div class="alert alert-danger"><?= esc($errors['global']) ?></div>
<?php endif; ?>

<form method="POST" class="mt-4" style="max-width: 600px;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label class="form-label">Выберите новую дату *</label>
        <input type="date" id="appointment_date" name="appointment_date" min="<?= date('Y-m-d') ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label d-block">Доступные слоты времени *</label>
        <div id="slots_container" class="p-3 border rounded bg-light text-muted">
            Выберите дату для генерации свободных интервалов.
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
    <a href="index.php?entity=appointment&action=list" class="btn btn-light">Отмена</a>
</form>

<script>
document.getElementById('appointment_date').addEventListener('change', function() {
    const date = this.value;
    const container = document.getElementById('slots_container');
    if(!date) return;
    container.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div> Поиск времени...';
    
    fetch('get_slots.php?date=' + date)
        .then(response => response.json())
        .then(slots => {
            container.innerHTML = '';
            if(slots.length === 0) {
                container.innerHTML = '<span class="text-danger">Нет свободного времени.</span>';
                return;
            }
            slots.forEach(time => {
                container.innerHTML += `
                    <div class="form-check form-check-inline m-1">
                        <input class="form-check-input" type="radio" name="appointment_time" id="t_${time}" value="${time}" required>
                        <label class="form-check-label badge bg-dark p-2 fs-6" for="t_${time}">${time}</label>
                    </div>
                `;
            });
        });
});
</script>
</body>
</html>