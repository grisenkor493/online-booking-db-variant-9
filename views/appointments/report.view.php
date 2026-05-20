<?php require 'views/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Статистика и аналитика центра</h2>
    <a href="index.php?entity=appointment&action=report&export=csv" class="btn btn-dark">📥 Скачать CSV-отчет</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">Записи по дням</div>
            <div class="card-body">
                <table class="table table-sm table-striped">
                    <thead><tr><th>Дата</th><th>Количество записей</th></tr></thead>
                    <tbody>
                        <?php foreach($byDays as $d): ?>
                            <tr><td><?= esc($d['date_row']) ?></td><td><span class="badge bg-secondary"><?= $d['total_bookings'] ?></span></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white fw-bold">Популярность языковых курсов</div>
            <div class="card-body">
                <table class="table table-sm table-striped">
                    <thead><tr><th>Название курса</th><th>Активных бронирований</th></tr></thead>
                    <tbody>
                        <?php foreach($byCourses as $c): ?>
                            <tr><td><?= esc($c['course_title']) ?></td><td><span class="badge bg-success"><?= $c['total_bookings'] ?></span></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>