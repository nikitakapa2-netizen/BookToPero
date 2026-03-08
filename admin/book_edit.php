<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$id=(int)($_GET['id']??0); $book=fetchBookById($id); if(!$book){ exit('Книга не найдена'); }
$categories=fetchCategories();
if($_SERVER['REQUEST_METHOD']==='POST'){
$data=['category_id'=>(int)$_POST['category_id'],'title'=>trim($_POST['title']),'author'=>trim($_POST['author']),'price'=>(float)$_POST['price'],'quantity'=>(int)$_POST['quantity'],'short_description'=>trim($_POST['short_description']),'full_description'=>trim($_POST['full_description']),'image'=>trim($_POST['image']),'is_new'=>isset($_POST['is_new'])?1:0,'is_popular'=>isset($_POST['is_popular'])?1:0,'is_recommended'=>isset($_POST['is_recommended'])?1:0];
if($data['price']<=0||$data['quantity']<0) setFlash('danger','Проверьте цену и количество.');
elseif(updateBook($id,$data)){ setFlash('success','Книга обновлена.'); redirect('books.php'); }
else setFlash('danger','Ошибка обновления.');
$book=array_merge($book,$data);
}
$pageTitle='Редактирование книги'; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Редактирование книги</h1><?php include __DIR__.'/_book_form.php'; ?></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
