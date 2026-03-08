<div class="card book-card h-100">
    <a href="book.php?id=<?= $book['id'] ?>" class="text-decoration-none text-dark"><img src="<?= e($book['image']) ?>" class="book-cover card-img-top" alt=""></a>
    <div class="card-body d-flex flex-column">
        <a href="book.php?id=<?= $book['id'] ?>" class="text-decoration-none text-dark fw-semibold d-block mb-1"><?= e($book['title']) ?></a>
        <div class="small text-muted mb-1"><?= e($book['author']) ?></div>
        <?php if ((int)$book['discount_percent'] > 0): ?><div class="small text-danger mb-1">Скидка <?= (int)$book['discount_percent'] ?>%</div><?php endif; ?>
        <?php if ((int)$book['is_coming_soon'] === 1): ?><div class="small text-warning mb-1">Скоро в продаже</div><?php endif; ?>
        <div class="mt-auto d-flex justify-content-between align-items-center">
            <strong><?= formatPrice((float)$book['price']) ?></strong>
            <div class="d-flex align-items-center gap-1">
                <form method="post" class="m-0">
                    <input type="hidden" name="action" value="toggle_wishlist"><input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <button class="wishlist-btn" title="В желаемое"><i class="bi <?= inWishlist((int)$book['id']) ? 'bi-heart-fill' : 'bi-heart' ?>"></i></button>
                </form>
                <form method="post" class="m-0">
                    <input type="hidden" name="action" value="add_to_cart"><input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <button class="btn btn-primary btn-sm" <?= (int)$book['is_coming_soon']===1 ? 'disabled' : '' ?>>В корзину</button>
                </form>
            </div>
        </div>
    </div>
</div>
