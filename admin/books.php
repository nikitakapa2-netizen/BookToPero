<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$books=fetchBooks();
$pageTitle='Книги'; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><div class="d-flex justify-content-between"><h1>Книги</h1><a class="btn btn-primary" href="book_create.php">Добавить книгу</a></div><table class="table bg-white"><thead><tr><th>ID</th><th>Название</th><th>Автор</th><th>Цена</th><th></th></tr></thead><tbody><?php foreach($books as $b): ?><tr><td><?=$b['id']?></td><td><?=e($b['title'])?></td><td><?=e($b['author'])?></td><td><?=formatPrice((float)$b['price'])?></td><td><a class="btn btn-sm btn-outline-primary" href="book_edit.php?id=<?=$b['id']?>">Редактировать</a> <a class="btn btn-sm btn-outline-danger" data-confirm="Удалить книгу?" href="book_delete.php?id=<?=$b['id']?>">Удалить</a></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
