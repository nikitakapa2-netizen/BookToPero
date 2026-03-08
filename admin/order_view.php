<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$orderId=(int)($_GET['id']??0);
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='chat'){
    $message=trim($_POST['message']??'');
    if($message!=='') createOrderMessage($orderId,(int)currentUser()['id'],$message);
    redirect('order_view.php?id='.$orderId);
}
$order=fetchOrderById($orderId,0,true); if(!$order) exit('Заказ не найден');
$messages=fetchOrderMessages($orderId);
$pageTitle='Заказ '.$order['order_number']; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Заказ №<?=e($order['order_number'])?></h1><p>Клиент: <?=e($order['full_name'])?> (<?=e($order['phone'])?>)</p><p>Статус: <?=e($order['status_name'])?></p><table class="table bg-white"><thead><tr><th>Книга</th><th>Количество</th><th>Цена</th></tr></thead><tbody><?php foreach($order['items'] as $item): ?><tr><td><?=e($item['title'])?></td><td><?=$item['quantity']?></td><td><?=formatPrice((float)$item['price'])?></td></tr><?php endforeach; ?></tbody></table>
<div class="bg-white p-3 rounded shadow-sm"><h5>Чат с клиентом</h5><div class="mb-3" style="max-height:220px;overflow:auto"><?php foreach($messages as $m): ?><div class="border rounded p-2 mb-2"><div class="small text-muted"><?=e($m['role_name'])?> · <?=e($m['full_name'])?> · <?=e($m['created_at'])?></div><div><?=e($m['message'])?></div></div><?php endforeach; ?></div><form method="post" class="d-flex gap-2"><input type="hidden" name="action" value="chat"><input class="form-control" name="message" placeholder="Введите сообщение"><button class="btn btn-primary">Отправить</button></form></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
