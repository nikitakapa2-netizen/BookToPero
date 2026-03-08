<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/queries.php';
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/includes/auth.php';
requireAuth();

$user = currentUser();
$orderId = (int)($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'chat') {
    $message = trim($_POST['message'] ?? '');
    if ($message !== '') createOrderMessage($orderId, (int)$user['id'], $message);
    redirect('order_details.php?id=' . $orderId);
}

$order=fetchOrderById($orderId, (int)$user['id'], isAdmin());
if(!$order){http_response_code(404);include __DIR__.'/404.php';exit;}
$messages = fetchOrderMessages($orderId);
$pageTitle='Заказ '.$order['order_number']; include __DIR__.'/includes/header.php'; ?>
<div class="container py-4"><h1>Заказ №<?=e($order['order_number'])?></h1><p>Статус: <strong><?=e($order['status_name'])?></strong></p><p>Дата: <?=e($order['created_at'])?></p><p>Способ получения: <?=e(deliveryLabel($order['delivery_method']))?></p><p>Пункт самовывоза: <?=e($order['pickup_point']?:'-')?></p><p>Адрес: <?=e($order['delivery_address']?:'самовывоз')?></p><table class="table bg-white"><thead><tr><th>Книга</th><th>Кол-во</th><th>Цена</th></tr></thead><tbody><?php foreach($order['items'] as $i): ?><tr><td><?=e($i['title'])?></td><td><?=$i['quantity']?></td><td><?=formatPrice((float)$i['price'])?></td></tr><?php endforeach; ?></tbody></table>
<div class="bg-white p-3 rounded shadow-sm"><h5>Чат по заказу</h5><div class="mb-3" style="max-height:220px;overflow:auto"><?php foreach($messages as $m): ?><div class="border rounded p-2 mb-2"><div class="small text-muted"><?=e($m['role_name'])?> · <?=e($m['full_name'])?> · <?=e($m['created_at'])?></div><div><?=e($m['message'])?></div></div><?php endforeach; ?></div><form method="post" class="d-flex gap-2"><input type="hidden" name="action" value="chat"><input class="form-control" name="message" placeholder="Введите сообщение"><button class="btn btn-primary">Отправить</button></form></div></div>
<?php include __DIR__.'/includes/footer.php'; ?>
