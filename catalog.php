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
<?php include __DIR__ . '/includes/footer.php'; ?>
