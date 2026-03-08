<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/queries.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();

if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='reset_password'){
    $id=(int)$_POST['user_id'];
    $newPass=trim($_POST['new_password']??'');
    if(mb_strlen($newPass)<6) setFlash('danger','Минимум 6 символов.');
    else { updateUserPassword($id,password_hash($newPass,PASSWORD_DEFAULT)); setFlash('success','Пароль пользователя обновлён.'); }
    redirect('users.php');
}

$users=fetchUsers();
$pageTitle='Пользователи'; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Пользователи</h1><table class="table bg-white"><thead><tr><th>ФИО</th><th>Логин</th><th>Email</th><th>Роль</th><th>Сброс пароля</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?=e($u['full_name'])?></td><td><?=e($u['login'])?></td><td><?=e($u['email'])?></td><td><?=e($u['role_name'])?></td><td><form method="post" class="d-flex gap-2"><input type="hidden" name="action" value="reset_password"><input type="hidden" name="user_id" value="<?=$u['id']?>"><input class="form-control form-control-sm" name="new_password" placeholder="Новый пароль"><button class="btn btn-sm btn-outline-primary">Сменить</button></form></td></tr><?php endforeach; ?></tbody></table></div>
$users=fetchUsers();
$pageTitle='Пользователи'; $assetPrefix = '../'; $rootPrefix = '../'; include __DIR__.'/../includes/header.php'; ?>
<div class="container py-4"><h1>Пользователи</h1><table class="table bg-white"><thead><tr><th>ФИО</th><th>Логин</th><th>Email</th><th>Роль</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?=e($u['full_name'])?></td><td><?=e($u['login'])?></td><td><?=e($u['email'])?></td><td><?=e($u['role_name'])?></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
