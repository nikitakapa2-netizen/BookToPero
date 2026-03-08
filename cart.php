<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['qty'] as $bookId => $qty) { updateCartItem((int)$bookId, (int)$qty); }
    }
    if (isset($_POST['remove'])) removeCartItem((int)$_POST['remove']);
    setFlash('success', 'Корзина обновлена.');
    redirect('cart.php');
}

$cart = cartItems();
$books = fetchBooksByIds(array_keys($cart));
$total = 0;
$pageTitle = 'Корзина';
include __DIR__ . '/includes/header.php';
?>
<div class="container py-4"><h1>Корзина</h1>
<?php if(!$cart): ?><div class="alert alert-info">Корзина пуста.</div><?php else: ?>
<form method="post"><table class="table bg-white shadow-sm"><thead><tr><th>Книга</th><th>Цена</th><th>Количество</th><th>Сумма</th><th>Наличие</th><th></th></tr></thead><tbody>
<?php foreach($cart as $id=>$qty): $book=$books[$id]??null; if(!$book) continue; $sum=$qty*$book['price']; $total+=$sum; ?>
<tr>
<td><?=e($book['title'])?></td>
<td><?=formatPrice((float)$book['price'])?></td>
<td style="width:120px"><input class="form-control" type="number" min="1" name="qty[<?=$id?>]" value="<?=$qty?>"></td>
<td><?=formatPrice((float)$sum)?></td>
<td>
  <?php if((int)$book['quantity']<=0): ?><span class="text-danger">Нет в наличии</span><?php else: ?><span class="text-success">Есть</span><?php endif; ?>
  <?php if((int)$book['is_pickup_available']!==1): ?><div class="small text-warning">Самовывоз недоступен</div><?php endif; ?>
</td>
<td><button class="btn btn-sm btn-outline-danger" name="remove" value="<?=$id?>">Удалить</button></td>
</tr>
<?php endforeach; ?></tbody></table><div class="d-flex justify-content-between"><h4>Итого: <?=formatPrice((float)$total)?></h4><div><button class="btn btn-outline-primary" name="update" value="1">Обновить</button> <a class="btn btn-primary" href="checkout.php">Оформить заказ</a></div></div></form>
<?php endif; ?></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
