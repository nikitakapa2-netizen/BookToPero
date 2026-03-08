<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_to_cart') {
    if (!isLoggedIn()) { setFlash('warning', 'Для добавления в корзину войдите или зарегистрируйтесь.'); redirect('login.php'); }
    addToCart((int)$_POST['book_id'], 1);
    setFlash('success', 'Книга добавлена в корзину.');
    redirect('catalog.php?' . http_build_query($_GET));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_wishlist') {
    toggleWishlist((int)$_POST['book_id']);
    setFlash('success', 'Список желаемого обновлён.');
    redirect('catalog.php?' . http_build_query($_GET));
if (($_GET['action'] ?? '') === 'add') {
    $bookId = (int)($_GET['id'] ?? 0);
    if (!isLoggedIn()) {
        setFlash('warning', 'Для добавления в корзину войдите или зарегистрируйтесь.');
        redirect('login.php');
    }
    addToCart($bookId, 1);
    setFlash('success', 'Книга добавлена в корзину.');
    redirect('cart.php');
}

$filters = [
    'category_id' => $_GET['category_id'] ?? '',
    'search' => trim($_GET['search'] ?? ''),
    'author' => trim($_GET['author'] ?? ''),
    'price_min' => $_GET['price_min'] ?? '',
    'price_max' => $_GET['price_max'] ?? '',
    'sort' => $_GET['sort'] ?? 'new'
];
$books = fetchBooks($filters);
$categories = fetchCategories();
$pageTitle = 'Каталог';
include __DIR__ . '/includes/header.php';
?>
<div class="container py-4">
    <h1 class="mb-3">Каталог книг</h1>
    <form class="row g-2 mb-4">
        <div class="col-md-3"><input class="form-control" name="search" value="<?= e($filters['search']) ?>" placeholder="По названию"></div>
        <div class="col-md-2"><input class="form-control" name="author" value="<?= e($filters['author']) ?>" placeholder="По автору"></div>
        <div class="col-md-2"><select class="form-select" name="category_id"><option value="">Все категории</option><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $filters['category_id']==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-1"><input class="form-control" name="price_min" placeholder="от" value="<?= e((string)$filters['price_min']) ?>"></div>
        <div class="col-md-1"><input class="form-control" name="price_max" placeholder="до" value="<?= e((string)$filters['price_max']) ?>"></div>
        <div class="col-md-2"><select class="form-select" name="sort"><option value="new">Новые</option><option value="price_asc" <?= $filters['sort']==='price_asc'?'selected':'' ?>>Цена ↑</option><option value="price_desc" <?= $filters['sort']==='price_desc'?'selected':'' ?>>Цена ↓</option><option value="title" <?= $filters['sort']==='title'?'selected':'' ?>>Название</option></select></div>
        <div class="col-md-1"><button class="btn btn-primary w-100">OK</button></div>
    </form>
    <div class="row g-3">
        <?php if (!$books): ?><div class="col-12"><div class="alert alert-info">Нет книг в данной категории.</div></div><?php endif; ?>
        <?php foreach ($books as $book): ?><div class="col-6 col-md-4 col-xl-3"><?php include __DIR__ . '/partials/book_card.php'; ?></div><?php endforeach; ?>
    </div>
</div>
<h1 class="mb-3">Каталог книг</h1>
<form class="row g-2 mb-4">
<div class="col-md-4"><input class="form-control" name="search" value="<?=e($filters['search'])?>" placeholder="Поиск по названию и автору"></div>
<div class="col-md-3"><select class="form-select" name="category_id"><option value="">Все категории</option><?php foreach($categories as $c): ?><option value="<?=$c['id']?>" <?=$filters['category_id']==$c['id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><select class="form-select" name="sort"><option value="new">Сначала новые</option><option value="price_asc" <?=$filters['sort']==='price_asc'?'selected':''?>>Цена по возрастанию</option><option value="price_desc" <?=$filters['sort']==='price_desc'?'selected':''?>>Цена по убыванию</option><option value="title" <?=$filters['sort']==='title'?'selected':''?>>По названию</option></select></div>
<div class="col-md-2"><button class="btn btn-primary w-100">Применить</button></div>
</form>
<div class="row g-4">
<?php if (!$books): ?><div class="col-12"><div class="alert alert-info">Нет книг в данной категории.</div></div><?php endif; ?>
<?php foreach($books as $book): ?>
<div class="col-md-4 col-lg-3"><div class="card book-card h-100"><img src="<?=e($book['image'])?>" class="book-cover card-img-top"><div class="card-body d-flex flex-column"><h6><?=e($book['title'])?></h6><small><?=e($book['author'])?></small><small class="text-muted"><?=e($book['category_name'])?></small><p class="small mt-2"><?=e($book['short_description'])?></p><div class="mt-auto"><strong><?=formatPrice((float)$book['price'])?></strong><div class="d-grid gap-2 mt-2"><a class="btn btn-outline-primary btn-sm" href="book.php?id=<?=$book['id']?>">Подробнее</a><a class="btn btn-primary btn-sm" href="catalog.php?action=add&id=<?=$book['id']?>">В корзину</a></div></div></div></div></div>
<?php endforeach; ?>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
