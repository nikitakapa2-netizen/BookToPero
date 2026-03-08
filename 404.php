<?php require_once __DIR__.'/includes/config.php'; require_once __DIR__.'/includes/functions.php'; http_response_code(404); $pageTitle='404'; include __DIR__.'/includes/header.php'; ?>
<div class="container py-5 text-center"><h1 class="display-3">404</h1><p class="lead">Страница не найдена</p><p>Попробуйте вернуться на главную страницу.</p><a href="index.php" class="btn btn-primary">На главную</a></div>
<?php include __DIR__.'/includes/footer.php'; ?>
