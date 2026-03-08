<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/functions.php';
requireAuth();
$user=currentUser();
$pageTitle='Профиль'; include __DIR__.'/includes/header.php'; ?>
<div class="container py-4"><h1>Профиль</h1><div class="bg-white p-4 rounded shadow-sm"><p><b>ФИО:</b> <?=e($user['full_name'])?></p><p><b>Email:</b> <?=e($user['email'])?></p><p><b>Телефон:</b> <?=e($user['phone'])?></p><p><b>Логин:</b> <?=e($user['login'])?></p></div></div>
<?php include __DIR__.'/includes/footer.php'; ?>
