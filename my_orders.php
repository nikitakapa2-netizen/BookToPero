<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/queries.php';
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/includes/auth.php';
requireAuth();
$orders=fetchOrdersByUser(currentUser()['id']);
$pageTitle='Мои заказы'; include __DIR__.'/includes/header.php'; ?>
<div class="container py-4"><h1>Мои заказы</h1><table class="table bg-white shadow-sm"><thead><tr><th>№</th><th>Дата</th><th>Статус</th><th>Сумма</th><th></th></tr></thead><tbody><?php foreach($orders as $o): ?><tr><td><?=e($o['order_number'])?></td><td><?=e($o['created_at'])?></td><td><?=e($o['status_name'])?></td><td><?=formatPrice((float)$o['total_amount'])?></td><td><a class="btn btn-sm btn-outline-primary" href="order_details.php?id=<?=$o['id']?>">Подробнее</a></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/includes/footer.php'; ?>
