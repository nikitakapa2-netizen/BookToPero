<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){ updateOrderStatus((int)$_POST['order_id'],(int)$_POST['status_id']); setFlash('success','Статус обновлён.'); redirect('orders.php'); }
$filters=['status_id'=>$_GET['status_id']??'','order_number'=>trim($_GET['order_number']??'')];
$orders=fetchAllOrders($filters); $statuses=fetchStatuses();
$pageTitle='Заказы'; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Заказы</h1><form class="row g-2 mb-3"><div class="col-md-4"><input class="form-control" name="order_number" value="<?=e($filters['order_number'])?>" placeholder="Поиск по номеру"></div><div class="col-md-4"><select class="form-select" name="status_id"><option value="">Все статусы</option><?php foreach($statuses as $s): ?><option value="<?=$s['id']?>" <?=$filters['status_id']==$s['id']?'selected':''?>><?=e($s['name'])?></option><?php endforeach; ?></select></div><div class="col-md-2"><button class="btn btn-primary w-100">Фильтр</button></div></form><table class="table bg-white"><thead><tr><th>Номер</th><th>Пользователь</th><th>Статус</th><th>Сумма</th><th></th></tr></thead><tbody><?php foreach($orders as $o): ?><tr><td><?=e($o['order_number'])?></td><td><?=e($o['login'])?></td><td><form method="post" class="d-flex gap-2"><input type="hidden" name="order_id" value="<?=$o['id']?>"><select class="form-select form-select-sm" name="status_id"><?php foreach($statuses as $s): ?><option value="<?=$s['id']?>" <?=$o['status_id']==$s['id']?'selected':''?>><?=e($s['name'])?></option><?php endforeach; ?></select><button class="btn btn-sm btn-outline-primary">OK</button></form></td><td><?=formatPrice((float)$o['total_amount'])?></td><td><a class="btn btn-sm btn-outline-secondary" href="order_view.php?id=<?=$o['id']?>">Детали</a></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
