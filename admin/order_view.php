<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$order=fetchOrderById((int)($_GET['id']??0),0,true); if(!$order) exit('Заказ не найден');
$pageTitle='Заказ '.$order['order_number']; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Заказ №<?=e($order['order_number'])?></h1><p>Клиент: <?=e($order['full_name'])?> (<?=e($order['phone'])?>)</p><p>Статус: <?=e($order['status_name'])?></p><table class="table bg-white"><thead><tr><th>Книга</th><th>Количество</th><th>Цена</th></tr></thead><tbody><?php foreach($order['items'] as $item): ?><tr><td><?=e($item['title'])?></td><td><?=$item['quantity']?></td><td><?=formatPrice((float)$item['price'])?></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
