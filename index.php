<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_to_cart') {
    if (!isLoggedIn()) {
        setFlash('warning', 'Для добавления в корзину войдите или зарегистрируйтесь.');
        redirect('login.php');
    }
    addToCart((int)($_POST['book_id'] ?? 0), 1);
    setFlash('success', 'Книга добавлена в корзину.');
    redirect('index.php?' . http_build_query($_GET));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_wishlist') {
    toggleWishlist((int)($_POST['book_id'] ?? 0));
    setFlash('success', 'Список желаемого обновлён.');
    redirect('index.php?' . http_build_query($_GET));
}

$filters = [
    'available' => (int)($_GET['available'] ?? 0),
    'is_new' => (int)($_GET['is_new'] ?? 0),
    'is_popular' => (int)($_GET['is_popular'] ?? 0),
    'has_discount' => (int)($_GET['has_discount'] ?? 0),
    'in_stock' => (int)($_GET['in_stock'] ?? 0),
    'search' => trim($_GET['search'] ?? ''),
    'author' => trim($_GET['author'] ?? ''),
    'price_min' => $_GET['price_min'] ?? '',
    'price_max' => $_GET['price_max'] ?? '',
    'publisher' => trim($_GET['publisher'] ?? ''),
    'publish_year' => trim($_GET['publish_year'] ?? ''),
    'binding_type' => trim($_GET['binding_type'] ?? ''),
    'paper_type' => trim($_GET['paper_type'] ?? ''),
    'language' => trim($_GET['language'] ?? ''),
    'sort' => $_GET['sort'] ?? 'new',
];

$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$totalBooks = countBooks($filters);
$pages = max(1, (int)ceil($totalBooks / $perPage));
$page = min($page, $pages);
$offset = ($page - 1) * $perPage;

$books = fetchBooks($filters, $perPage, $offset);
$newBooks = fetchFeaturedBooks('is_new', 8);
$comingSoonBooks = fetchFeaturedBooks('is_coming_soon', 6);
$reviews = fetchReviews(8);

$popularCategoryNames = ['Деловая литература', 'Детективы'];
$popularCategoryBooks = [];
foreach ($popularCategoryNames as $catName) {
    $allCats = fetchCategories();
    foreach ($allCats as $cat) {
        if ($cat['name'] === $catName) {
            $popularCategoryBooks[$catName] = fetchBooks(['category_id' => $cat['id'], 'sort' => 'new'], 2, 0);
        }
    }
}

$sectionNames = [
    'Книги','Художественная литература','Литература Казахстана','Детская литература','Образование','Учебники','Популярная психология','Деловая литература','Дом. Семья. Досуг','Эзотерика. Астрология и нумерология','Книги на иностранных языках','Журналы и газеты','Медицина и здоровье','Наука','Публицистика','Подарочные издания','Компьютерная литература','Юридическая литература','Техническая литература','Искусство. Культура','Спорт. Туризм. Хобби','Энциклопедии. Справочники','Автомобили','Тайны, сенсации, катастрофы','Аксессуары, календари, открытки','Аудиокниги','Уцененные книги'
];

$popularAuthors = ['Ф. М. Достоевский', 'Л. Н. Толстой', 'С. Кинг', 'Р. Скоттон', 'А. Кристи'];

$pageTitle = 'Главная — ' . APP_NAME;
include __DIR__ . '/includes/header.php';
?>
<div class="container pb-5">
    <div class="row g-4 mt-1">
        <aside class="col-lg-3">
            <div class="bg-white rounded-4 shadow-sm p-3 sticky-top-catalog">
                <h5 class="mb-3">Фильтр</h5>
                <a class="filter-link <?= $filters['available'] ? 'active':'' ?>" href="?<?= e(http_build_query(array_merge($_GET,['available'=>$filters['available']?0:1,'page'=>1]))) ?>">Доступен</a>
                <a class="filter-link <?= $filters['is_new'] ? 'active':'' ?>" href="?<?= e(http_build_query(array_merge($_GET,['is_new'=>$filters['is_new']?0:1,'page'=>1]))) ?>">Новинка</a>
                <a class="filter-link <?= $filters['is_popular'] ? 'active':'' ?>" href="?<?= e(http_build_query(array_merge($_GET,['is_popular'=>$filters['is_popular']?0:1,'page'=>1]))) ?>">Лидер продаж</a>
                <a class="filter-link <?= $filters['has_discount'] ? 'active':'' ?>" href="?<?= e(http_build_query(array_merge($_GET,['has_discount'=>$filters['has_discount']?0:1,'page'=>1]))) ?>">Товар со скидкой</a>
                <a class="filter-link <?= $filters['in_stock'] ? 'active':'' ?>" href="?<?= e(http_build_query(array_merge($_GET,['in_stock'=>$filters['in_stock']?0:1,'page'=>1]))) ?>">Есть на складе</a>

                <hr>
                <h5 class="mb-2">Разделы</h5>
                <?php foreach ($sectionNames as $name): ?>
                    <a class="filter-link" href="catalog.php?search=<?= urlencode($name) ?>"><?= e($name) ?></a>
                <?php endforeach; ?>

                <hr>
                <h6>Поиск по цене</h6>
                <form class="mb-3">
                    <div class="d-flex gap-2"><input name="price_min" class="form-control" placeholder="от" value="<?= e((string)$filters['price_min']) ?>"><input name="price_max" class="form-control" placeholder="до" value="<?= e((string)$filters['price_max']) ?>"></div>
                    <button class="btn btn-primary btn-sm w-100 mt-2">Применить</button>
                </form>

                <h6>Автор</h6>
                <form class="mb-2"><input class="form-control" name="author" value="<?= e($filters['author']) ?>" placeholder="Введите автора"></form>
                <div class="d-grid gap-1 mb-3">
                    <?php foreach ($popularAuthors as $author): ?>
                        <a class="btn btn-outline-secondary btn-sm" href="?<?= e(http_build_query(array_merge($_GET,['author'=>$author,'page'=>1]))) ?>"><?= e($author) ?></a>
                    <?php endforeach; ?>
                </div>

                <form>
                    <input class="form-control mb-2" name="publisher" value="<?= e($filters['publisher']) ?>" placeholder="Издательство">
                    <input class="form-control mb-2" name="publish_year" value="<?= e($filters['publish_year']) ?>" placeholder="Дата выхода (год)">
                    <input class="form-control mb-2" name="binding_type" value="<?= e($filters['binding_type']) ?>" placeholder="Переплет">
                    <input class="form-control mb-2" name="paper_type" value="<?= e($filters['paper_type']) ?>" placeholder="Бумага">
                    <input class="form-control mb-2" name="language" value="<?= e($filters['language']) ?>" placeholder="Язык">
                    <button class="btn btn-outline-primary btn-sm w-100">Фильтровать</button>
                </form>
            </div>
        </aside>

        <section class="col-lg-9">
            <h1 class="mb-3">Книги</h1>
            <div class="row g-3 mb-4">
                <div class="col-md-8"><div class="hero-banner p-4 rounded-4 shadow-sm"><h2 class="display-6 fw-bold">Успех начинается с книг</h2><p class="mb-2">Скидки до 30% на популярные издания недели.</p><a href="catalog.php" class="btn btn-primary">Перейти в каталог</a></div></div>
                <div class="col-md-4 d-grid gap-3"><div class="mini-banner mini-banner-blue rounded-4 p-3 text-white">-20% на книги издательства</div><div class="mini-banner mini-banner-red rounded-4 p-3 text-white">-20% на книжные новинки</div></div>
            </div>

            <h3 class="section-title">Книжные новинки</h3>
            <div class="row g-3 mb-4"><?php foreach($newBooks as $book): ?><div class="col-6 col-md-4 col-xl-3"><?php include __DIR__ . '/partials/book_card.php'; ?></div><?php endforeach; ?></div>

            <h3 class="section-title">Частые категории</h3>
            <div class="row g-3 mb-4">
                <?php foreach ($popularCategoryBooks as $catName => $items): ?>
                    <div class="col-md-6"><div class="category-banner"><h5><?= e($catName) ?></h5><div class="row g-2"><?php foreach ($items as $book): ?><div class="col-6"><?php include __DIR__ . '/partials/book_mini.php'; ?></div><?php endforeach; ?></div></div></div>
                <?php endforeach; ?>
            </div>

            <h3 class="section-title">Все книги</h3>
            <div class="row g-3 mb-3"><?php foreach($books as $book): ?><div class="col-6 col-md-4 col-xl-3"><?php include __DIR__ . '/partials/book_card.php'; ?></div><?php endforeach; ?></div>
            <nav><ul class="pagination justify-content-center">
                <?php for($i=1;$i<=$pages;$i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?<?= e(http_build_query(array_merge($_GET,['page'=>$i]))) ?>"><?= $i ?></a></li><?php endfor; ?>
            </ul></nav>

            <h3 class="section-title">Будущие книги</h3>
            <div class="row g-3 mb-4"><?php foreach($comingSoonBooks as $book): ?><div class="col-6 col-md-4 col-xl-3"><?php include __DIR__ . '/partials/book_card.php'; ?></div><?php endforeach; ?></div>

            <section class="my-5 p-4 bg-white rounded-4 shadow-sm"><h3>История создания магазина</h3><p class="mb-0">«Лист и Перо» начался как учебный проект и превратился в полноценный демонстрационный интернет-магазин с удобной витриной, корзиной и админ-панелью.</p></section>
        </section>
    </div>

    <h2 class="section-title">Отзывы</h2>
    <div id="reviewsCarousel" class="carousel slide review-carousel" data-bs-ride="carousel" data-bs-interval="4000">
      <div class="carousel-inner bg-white rounded-4 shadow-sm p-3">
        <?php foreach ($reviews as $idx => $review): ?>
            <div class="carousel-item <?= $idx===0?'active':'' ?>">
                <strong><?= e($review['full_name']) ?></strong>
                <span class="badge badge-soft">★ <?= (int)$review['rating'] ?>/5</span>
                <p class="mt-2 mb-0"><?= e($review['review_text']) ?></p>
            </div>
        <?php endforeach; ?>
      </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
