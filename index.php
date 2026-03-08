<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'contact') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($full_name === '' || $email === '' || $message === '') {
        setFlash('danger', 'Заполните все поля формы обратной связи.');
    } elseif (!validateEmail($email)) {
        setFlash('danger', 'Введите корректный email.');
    } elseif (createContact($full_name, $email, $message)) {
        setFlash('success', 'Спасибо! Сообщение отправлено.');
    } else {
        setFlash('warning', 'Сервис обратной связи временно недоступен.');
    }
    redirect('index.php#contact-form');
}

$pageTitle = 'Главная — ' . APP_NAME;
$newBooks = fetchFeaturedBooks('is_new');
$popularBooks = fetchFeaturedBooks('is_popular');
$recommendedBooks = fetchFeaturedBooks('is_recommended');
$categories = fetchCategories();
$reviews = fetchReviews();
include __DIR__ . '/includes/header.php';
?>
<div class="container">
    <section class="hero mt-3">
        <h1 class="display-5 fw-bold">Добро пожаловать в «Лист и Перо»</h1>
        <p class="lead">Уютный интернет-магазин книг: от классики до бизнес-литературы.</p>
        <a href="catalog.php" class="btn btn-primary btn-lg">Перейти в каталог</a>
    </section>

    <h2 class="section-title">Категории</h2>
    <div class="row g-2"><?php foreach ($categories as $cat): ?><div class="col-6 col-md-3"><a class="btn btn-light w-100 shadow-sm" href="catalog.php?category_id=<?= $cat['id'] ?>"><?= e($cat['name']) ?></a></div><?php endforeach; ?></div>

    <?php foreach (['Новинки'=>$newBooks,'Популярное'=>$popularBooks,'Рекомендуем'=>$recommendedBooks] as $title=>$books): ?>
        <h2 class="section-title"><?= $title ?></h2>
        <div class="row g-4">
            <?php foreach ($books as $book): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card book-card h-100">
                        <img src="<?= e($book['image']) ?>" class="book-cover card-img-top" alt="cover">
                        <div class="card-body d-flex flex-column">
                            <h6><?= e($book['title']) ?></h6><small class="text-muted"><?= e($book['author']) ?></small>
                            <div class="mt-auto d-flex justify-content-between align-items-center"><strong><?= formatPrice((float)$book['price']) ?></strong><a class="btn btn-sm btn-outline-primary" href="book.php?id=<?= $book['id'] ?>">Подробнее</a></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <section class="my-5 p-4 bg-white rounded-4 shadow-sm">
        <h3>Почему выбирают нас</h3>
        <ul><li>Большой выбор книг</li><li>Быстрая обработка заказов</li><li>Понятный и удобный интерфейс</li></ul>
    </section>

    <h2 class="section-title">Отзывы</h2>
    <div class="row g-3"><?php foreach($reviews as $review): ?><div class="col-md-6"><div class="p-3 bg-white rounded-3 shadow-sm"><strong><?=e($review['full_name'])?></strong> <span class="badge badge-soft">★ <?= (int)$review['rating'] ?>/5</span><p class="mb-0 mt-2"><?=e($review['review_text'])?></p></div></div><?php endforeach; ?></div>

    <section class="my-5 p-4 bg-white rounded-4 shadow-sm" id="contact-form">
        <h3>Обратная связь</h3>
        <form method="post" class="row g-3">
            <input type="hidden" name="action" value="contact">
            <div class="col-md-4"><input class="form-control" name="full_name" placeholder="Имя" required></div>
            <div class="col-md-4"><input class="form-control" type="email" name="email" placeholder="Email" required></div>
            <div class="col-md-12"><textarea class="form-control" name="message" rows="4" placeholder="Сообщение" required></textarea></div>
            <div><button class="btn btn-primary">Отправить</button></div>
        </form>
    </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
