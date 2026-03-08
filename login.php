<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/queries.php';
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/includes/auth.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $login=trim($_POST['login']??''); $password=$_POST['password']??'';
    $user=fetchUserByLogin($login);
    if($user && password_verify($password,$user['password_hash'])){ loginUser($user); setFlash('success','Вы вошли в аккаунт.'); redirect('index.php'); }
    setFlash('danger','Неверный логин или пароль.');
}
$pageTitle='Вход'; include __DIR__.'/includes/header.php'; ?>
<div class="container py-5" style="max-width:460px"><h1>Вход</h1><form method="post" class="bg-white p-4 rounded shadow-sm"><div class="mb-3"><input class="form-control" name="login" required placeholder="Логин"></div><div class="mb-3"><input class="form-control" type="password" name="password" required placeholder="Пароль"></div><button class="btn btn-primary w-100">Войти</button><a class="btn btn-link w-100" href="register.php">Регистрация</a></form></div>
<?php include __DIR__.'/includes/footer.php'; ?>
