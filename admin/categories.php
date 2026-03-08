<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['name'])){ createCategory(trim($_POST['name'])); setFlash('success','Категория добавлена.'); redirect('categories.php'); }
if(isset($_GET['delete'])){ deleteCategory((int)$_GET['delete']); setFlash('success','Категория удалена.'); redirect('categories.php'); }
$categories=fetchCategories();
$pageTitle='Категории'; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Категории</h1><form method="post" class="d-flex gap-2 mb-3"><input class="form-control" name="name" required placeholder="Новая категория"><button class="btn btn-primary">Добавить</button></form><table class="table bg-white"><tbody><?php foreach($categories as $c): ?><tr><td><?=e($c['name'])?></td><td class="text-end"><a class="btn btn-sm btn-outline-danger" href="categories.php?delete=<?=$c['id']?>" data-confirm="Удалить категорию?">Удалить</a></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
