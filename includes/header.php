<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
$user = currentUser();
$assetPrefix = $assetPrefix ?? '';
$rootPrefix = $rootPrefix ?? '';
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $assetPrefix ?>assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $rootPrefix ?>index.php"><i class="bi bi-book-half"></i> Лист и Перо</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="<?= $rootPrefix ?>catalog.php">Каталог</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $rootPrefix ?>cart.php">Корзина</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $rootPrefix ?>contacts.php">Контакты</a></li>
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $rootPrefix ?>my_orders.php">Мои заказы</a></li>
                    <?php if ($user['role_name'] === 'admin'): ?><li class="nav-item"><a class="nav-link" href="<?= $rootPrefix ?>admin/index.php">Админ</a></li><?php endif; ?>
                    <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" href="<?= $rootPrefix ?>logout.php">Выход (<?= e($user['login']) ?>)</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?= $rootPrefix ?>login.php">Вход</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="pb-5">
<div class="container mt-3">
    <?php foreach (getFlashes() as $flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
</div>
