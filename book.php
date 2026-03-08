<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$book = fetchBookById($id);
if (!$book) { http_response_code(404); include __DIR__ . '/404.php'; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_to_cart') {
    if (!isLoggedIn()) { setFlash('warning', 'Для добавления в корзину войдите или зарегистрируйтесь.'); redirect('login.php'); }
    addToCart($id, max(1, (int)($_POST['quantity'] ?? 1)));
    setFlash('success', 'Книга добавлена в корзину.');
    redirect('book.php?id=' . $id);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_wishlist') {
    toggleWishlist($id); setFlash('success', 'Список желаемого обновлён.'); redirect('book.php?id=' . $id);
}

$similarBooks = fetchSimilarBooks($id, (int)$book['category_id'], 6);
$recommended = fetchRandomBooks(6);
$pageTitle = $book['title'];
include __DIR__ . '/includes/header.php';
?>
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4"><img class="img-fluid rounded-4 shadow-sm w-100" src="<?= e($book['image']) ?>" alt="<?= e($book['title']) ?>"></div>
        <div class="col-lg-5">
            <h1 class="mb-2"><?= e($book['title']) ?></h1>
            <div class="text-muted mb-2">Автор: <?= e($book['author']) ?></div>
            <div class="text-muted mb-2">Издательство: <?= e($book['publisher']) ?></div>
            <div class="text-muted mb-2">Дата выхода: <?= (int)$book['publish_year'] ?></div>
            <div class="text-muted mb-2">Категория: <?= e($book['category_name']) ?></div>
            <p><?= e($book['full_description']) ?></p>
            <div class="book-info-card mt-4"><h5>Подробнее</h5><ul class="mb-0"><li>Переплет: <?= e($book['binding_type']) ?></li><li>Бумага: <?= e($book['paper_type']) ?></li><li>Язык: <?= e($book['language']) ?></li><li>Номер в системе: <?= (int)$book['id'] ?></li></ul></div>
        </div>
        <div class="col-lg-3">
            <div class="bg-white rounded-4 shadow-sm p-3 sticky-summary">
                <h3 class="mb-3"><?= formatPrice((float)$book['price']) ?></h3>
                <form method="post" class="mb-2"><input type="hidden" name="action" value="toggle_wishlist"><button class="btn btn-outline-danger w-100"><i class="bi <?= inWishlist($id)?'bi-heart-fill':'bi-heart' ?>"></i> В желаемое</button></form>
                <form method="post"><input type="hidden" name="action" value="add_to_cart"><div class="mb-3"><label class="form-label">Количество</label><input type="number" class="form-control" name="quantity" min="1" max="<?= max(1,(int)$book['quantity']) ?>" value="1"></div><button class="btn btn-primary w-100" <?= (int)$book['is_coming_soon']===1 ? 'disabled' : '' ?>>Добавить в корзину</button></form>
            </div>
        </div>
    </div>

    <h3 class="section-title">Похожие книги</h3><div class="row g-3"><?php foreach($similarBooks as $book): ?><div class="col-md-4 col-lg-2"><?php include __DIR__.'/partials/book_mini.php'; ?></div><?php endforeach; ?></div>
    <h3 class="section-title">Может понравиться</h3><div class="row g-3"><?php foreach($recommended as $book): ?><div class="col-md-4 col-lg-2"><?php include __DIR__.'/partials/book_mini.php'; ?></div><?php endforeach; ?></div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
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
