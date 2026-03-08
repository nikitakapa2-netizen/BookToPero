<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$pageTitle='Админ-панель'; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Админ-панель</h1><div class="list-group"><a class="list-group-item" href="books.php">Книги</a><a class="list-group-item" href="categories.php">Категории</a><a class="list-group-item" href="orders.php">Заказы</a><a class="list-group-item" href="users.php">Пользователи</a><a class="list-group-item" href="contacts.php">Обратная связь</a></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
