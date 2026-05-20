<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Языковой центр — Панель управления</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Языковой центр</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['entity'] ?? 'client') === 'client' ? 'active' : '' ?>" href="index.php?entity=client&action=list">Клиенты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['entity'] ?? '') === 'course' ? 'active' : '' ?>" href="index.php?entity=course&action=list">Курсы</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['entity'] ?? '') === 'test' && ($_GET['action'] ?? '') === 'list' ? 'active' : '' ?>" href="index.php?entity=test&action=list">Тестирование</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning fw-bold <?= ($_GET['entity'] ?? '') === 'appointment' && ($_GET['action'] ?? '') === 'list' ? 'active' : '' ?>" href="index.php?entity=appointment&action=list">🗓️ Онлайн-запись</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_GET['action'] ?? '') === 'report' ? 'active' : '' ?>" href="index.php?entity=appointment&action=report">📊 Отчёты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger btn-sm text-white ms-2 px-3 <?= ($_GET['action'] ?? '') === 'queue' ? 'active' : '' ?>" href="index.php?entity=test&action=queue">⚠️ Очереди</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc($_SESSION['flash_success']) ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>
</div>

<div class="container bg-white p-4 rounded shadow-sm mb-5">