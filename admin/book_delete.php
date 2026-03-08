<?php
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
deleteBook((int)($_GET['id']??0));
setFlash('success','Книга удалена.');
redirect('books.php');
