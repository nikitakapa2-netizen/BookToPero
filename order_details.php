<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/queries.php';
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/includes/auth.php';
requireAuth();
$order=fetchOrderById((int)($_GET['id']??0), currentUser()['id'], isAdmin());
if(!$order){http_response_code(404);exit('Заказ не найден');}
$pageTitle='Заказ '.$order['order_number']; include __DIR__.'/includes/header.php'; ?>
<div class="container py-4"><h1>Заказ №<?=e($order['order_number'])?></h1><p>Статус: <strong><?=e($order['status_name'])?></strong></p><p>Дата: <?=e($order['created_at'])?></p><p>Способ получения: <?=e($order['delivery_method'])?></p><p>Адрес: <?=e($order['delivery_address']?:'самовывоз')?></p><table class="table bg-white"><thead><tr><th>Книга</th><th>Кол-во</th><th>Цена</th></tr></thead><tbody><?php foreach($order['items'] as $i): ?><tr><td><?=e($i['title'])?></td><td><?=$i['quantity']?></td><td><?=formatPrice((float)$i['price'])?></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/includes/footer.php'; ?>
