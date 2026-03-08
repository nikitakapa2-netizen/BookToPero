<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
$user = currentUser();
$assetPrefix = $assetPrefix ?? '';
$rootPrefix = $rootPrefix ?? '';
$cartQty = cartCount();
$wishQty = wishlistCount();
$isAdminArea = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/');
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
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $rootPrefix ?>index.php"><i class="bi bi-book-half"></i> Лист и Перо</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link <?= isActivePage('index.php') ? 'active' : '' ?>" href="<?= $rootPrefix ?>index.php">Главная</a></li>
                <li class="nav-item"><a class="nav-link <?= isActivePage('catalog.php') ? 'active' : '' ?>" href="<?= $rootPrefix ?>catalog.php">Каталог</a></li>
                <li class="nav-item"><a class="nav-link <?= isActivePage('contacts.php') ? 'active' : '' ?>" href="<?= $rootPrefix ?>contacts.php">Контакты</a></li>
                <li class="nav-item"><a class="nav-link <?= isActivePage('payment_methods.php') ? 'active' : '' ?>" href="<?= $rootPrefix ?>payment_methods.php">Способы оплаты</a></li>
                <li class="nav-item"><a class="nav-link <?= isActivePage('help.php') ? 'active' : '' ?>" href="<?= $rootPrefix ?>help.php">Помощь</a></li>
                <?php if (!($isAdminArea && $user && $user['role_name'] === 'admin')): ?>
                    <li class="nav-item"><a class="nav-link cart-icon-link" href="<?= $rootPrefix ?>wishlist.php" title="Желаемое"><i class="bi bi-heart fs-5"></i><?php if ($wishQty > 0): ?><span class="cart-badge"><?= $wishQty ?></span><?php endif; ?></a></li>
                    <li class="nav-item"><a class="nav-link cart-icon-link" href="<?= $rootPrefix ?>cart.php" title="Корзина"><i class="bi bi-cart3 fs-5"></i><?php if ($cartQty > 0): ?><span class="cart-badge"><?= $cartQty ?></span><?php endif; ?></a></li>
                <?php endif; ?>

                <?php if ($user): ?>
                    <?php if (!($isAdminArea && $user['role_name'] === 'admin')): ?>
                        <li class="nav-item"><a class="nav-link <?= isActivePage('my_orders.php') ? 'active' : '' ?>" href="<?= $rootPrefix ?>my_orders.php">Мои заказы</a></li>
                    <?php endif; ?>
                    <?php if ($user['role_name'] === 'admin'): ?><li class="nav-item"><a class="nav-link" href="<?= $rootPrefix ?>admin/index.php">Админ</a></li><?php endif; ?>
                    <li class="nav-item d-flex align-items-center gap-2">
                        <img src="<?= $rootPrefix . e($user['avatar'] ?: 'assets/img/avatars/default-avatar.svg') ?>" class="nav-avatar" alt="avatar">
                        <a class="nav-link <?= isActivePage('profile.php') ? 'active' : '' ?>" href="<?= $rootPrefix ?>profile.php"><?= e($user['full_name']) ?></a>
                    </li>
                    <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" href="<?= $rootPrefix ?>logout.php">Выход</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?= $rootPrefix ?>login.php">Вход</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="pb-5 flex-grow-1">
<div class="container mt-3">
    <?php foreach (getFlashes() as $flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
</div>
