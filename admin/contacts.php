<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$contacts=fetchContacts();
$pageTitle='Сообщения'; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Сообщения обратной связи</h1><table class="table bg-white"><thead><tr><th>Имя</th><th>Email</th><th>Сообщение</th><th>Дата</th></tr></thead><tbody><?php foreach($contacts as $c): ?><tr><td><?=e($c['full_name'])?></td><td><?=e($c['email'])?></td><td><?=e($c['message'])?></td><td><?=e($c['created_at'])?></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
