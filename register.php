<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/queries.php';
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/includes/auth.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $data=['full_name'=>trim($_POST['full_name']??''),'email'=>trim($_POST['email']??''),'phone'=>trim($_POST['phone']??''),'login'=>trim($_POST['login']??''),'password_hash'=>''];
    $password=$_POST['password']??'';
    if(in_array('',[$data['full_name'],$data['email'],$data['phone'],$data['login']],true) || $password==='') setFlash('danger','Заполните все поля.');
    elseif(!validateEmail($data['email'])) setFlash('danger','Некорректный email.');
    elseif(!validatePhone($data['phone'])) setFlash('danger','Некорректный телефон.');
    elseif(mb_strlen($password)<6) setFlash('danger','Пароль должен быть не короче 6 символов.');
    elseif(emailOrLoginExists($data['email'],$data['login'])) setFlash('danger','Пользователь с таким email или login уже существует.');
    else { $data['password_hash']=password_hash($password,PASSWORD_DEFAULT); if(createUser($data)){ setFlash('success','Регистрация завершена. Войдите в аккаунт.'); redirect('login.php'); } setFlash('danger','Ошибка регистрации.'); }
}
$pageTitle='Регистрация'; include __DIR__.'/includes/header.php'; ?>
<div class="container py-5" style="max-width:600px"><h1>Регистрация</h1><form method="post" class="bg-white p-4 rounded shadow-sm row g-3"><div class="col-12"><input class="form-control" name="full_name" required placeholder="ФИО" value="<?=old('full_name')?>"></div><div class="col-md-6"><input class="form-control" name="email" required type="email" placeholder="Email" value="<?=old('email')?>"></div><div class="col-md-6"><input class="form-control" name="phone" required placeholder="Телефон" value="<?=old('phone')?>"></div><div class="col-md-6"><input class="form-control" name="login" required placeholder="Логин" value="<?=old('login')?>"></div><div class="col-md-6"><input class="form-control" type="password" name="password" required placeholder="Пароль"></div><div class="col-12"><button class="btn btn-primary w-100">Создать аккаунт</button></div></form></div>
<?php include __DIR__.'/includes/footer.php'; ?>
