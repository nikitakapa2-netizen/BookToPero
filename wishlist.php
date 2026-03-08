<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/queries.php';
require_once __DIR__.'/includes/functions.php';

if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='toggle_wishlist') {
    toggleWishlist((int)$_POST['book_id']);
    setFlash('success','Список желаемого обновлён.');
    redirect('wishlist.php');
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add_to_cart') {
    require_once __DIR__.'/includes/auth.php';
    if (!isLoggedIn()) { setFlash('warning','Для добавления в корзину войдите.'); redirect('login.php'); }
    addToCart((int)$_POST['book_id'],1);
    setFlash('success','Книга добавлена в корзину.');
    redirect('wishlist.php');
}
$books = fetchBooksByIds(wishlistItems());
$pageTitle='Список желаемого'; include __DIR__.'/includes/header.php'; ?>
<div class="container py-4"><h1>Список желаемого</h1><div class="row g-3"><?php foreach($books as $book): ?><div class="col-6 col-md-4 col-xl-3"><?php include __DIR__.'/partials/book_card.php'; ?></div><?php endforeach; if(!$books): ?><div class="alert alert-info">Пока нет добавленных книг.</div><?php endif; ?></div></div>
<?php include __DIR__.'/includes/footer.php'; ?>
