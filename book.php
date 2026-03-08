<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$id=(int)($_GET['id']??0);
$book=fetchBookById($id);
if(!$book){http_response_code(404);exit('Книга не найдена');}
if(($_GET['action']??'')==='add'){
 if(!isLoggedIn()){ setFlash('warning','Для добавления в корзину войдите или зарегистрируйтесь.'); redirect('login.php'); }
 addToCart($id,1); setFlash('success','Товар добавлен в корзину.'); redirect('cart.php');
}
$pageTitle=$book['title']; include __DIR__.'/includes/header.php'; ?>
<div class="container py-4"><div class="row g-4"><div class="col-md-4"><img class="img-fluid rounded shadow" src="<?=e($book['image'])?>"></div><div class="col-md-8"><h1><?=e($book['title'])?></h1><p class="text-muted"><?=e($book['author'])?> · <?=e($book['category_name'])?></p><p><?=e($book['full_description'])?></p><h3><?=formatPrice((float)$book['price'])?></h3><p>Остаток: <?= (int)$book['quantity'] ?></p><a class="btn btn-primary" href="book.php?id=<?=$book['id']?>&action=add">В корзину</a></div></div></div>
<?php include __DIR__.'/includes/footer.php'; ?>
