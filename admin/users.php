<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$users=fetchUsers();
$pageTitle='Пользователи'; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Пользователи</h1><table class="table bg-white"><thead><tr><th>ФИО</th><th>Логин</th><th>Email</th><th>Роль</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?=e($u['full_name'])?></td><td><?=e($u['login'])?></td><td><?=e($u['email'])?></td><td><?=e($u['role_name'])?></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
