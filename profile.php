<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireAuth();
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'profile') {
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($fullName === '' || $phone === '') {
            setFlash('danger', 'ФИО и телефон обязательны.');
            redirect('profile.php');
        }
        if (!validatePhone($phone)) {
            setFlash('danger', 'Введите корректный номер телефона.');
            redirect('profile.php');
        }

        $avatarPath = null;
        if (!empty($_FILES['avatar']['name']) && (int)$_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                setFlash('danger', 'Аватар должен быть в формате JPG, PNG или WEBP.');
                redirect('profile.php');
            }
            if ((int)$_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                setFlash('danger', 'Размер файла не должен превышать 2 МБ.');
                redirect('profile.php');
            }

            $fileName = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
            $dest = __DIR__ . '/assets/img/avatars/' . $fileName;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                $avatarPath = 'assets/img/avatars/' . $fileName;
            }
        }

        if (updateUserProfile((int)$user['id'], $fullName, $phone, $avatarPath)) {
            setFlash('success', 'Профиль обновлён.');
        } else {
            setFlash('danger', 'Не удалось обновить профиль.');
        }
        redirect('profile.php');
    }

    if (($_POST['action'] ?? '') === 'password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $user['password_hash'])) {
            setFlash('danger', 'Текущий пароль введён неверно.');
            redirect('profile.php');
        }
        if (mb_strlen($newPassword) < 6) {
            setFlash('danger', 'Новый пароль должен быть не короче 6 символов.');
            redirect('profile.php');
        }
        if ($newPassword !== $confirmPassword) {
            setFlash('danger', 'Подтверждение пароля не совпадает.');
            redirect('profile.php');
        }

        if (updateUserPassword((int)$user['id'], password_hash($newPassword, PASSWORD_DEFAULT))) {
            setFlash('success', 'Пароль успешно изменён. Теперь можно входить с новым паролем.');
        } else {
            setFlash('danger', 'Не удалось изменить пароль.');
        }
        redirect('profile.php');
    }
}

$user = currentUser();
$pageTitle = 'Личный кабинет';
include __DIR__ . '/includes/header.php';
?>
<div class="container py-4">
    <h1 class="mb-4">Личный кабинет</h1>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <img src="<?= e($user['avatar'] ?: 'assets/img/avatars/default-avatar.svg') ?>" alt="avatar" class="profile-avatar mb-3">
                    <h5 class="mb-1"><?= e($user['full_name']) ?></h5>
                    <div class="text-muted"><?= e($user['login']) ?></div>
                    <div class="small mt-2">Email: <?= e($user['email']) ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Редактирование профиля</h5>
                    <form method="post" enctype="multipart/form-data" class="row g-3">
                        <input type="hidden" name="action" value="profile">
                        <div class="col-md-6"><input class="form-control" name="full_name" value="<?= e($user['full_name']) ?>" required></div>
                        <div class="col-md-6"><input class="form-control" name="phone" value="<?= e($user['phone']) ?>" required></div>
                        <div class="col-12"><input class="form-control" type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp"></div>
                        <div class="col-12"><button class="btn btn-primary">Сохранить профиль</button></div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">Смена пароля</h5>
                    <form method="post" class="row g-3">
                        <input type="hidden" name="action" value="password">
                        <div class="col-md-4"><input class="form-control" type="password" name="current_password" placeholder="Текущий пароль" required></div>
                        <div class="col-md-4"><input class="form-control" type="password" name="new_password" placeholder="Новый пароль" required></div>
                        <div class="col-md-4"><input class="form-control" type="password" name="confirm_password" placeholder="Подтверждение" required></div>
                        <div class="col-12"><button class="btn btn-outline-primary">Изменить пароль</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/functions.php';
requireAuth();
$user=currentUser();
$pageTitle='Профиль'; include __DIR__.'/includes/header.php'; ?>
<div class="container py-4"><h1>Профиль</h1><div class="bg-white p-4 rounded shadow-sm"><p><b>ФИО:</b> <?=e($user['full_name'])?></p><p><b>Email:</b> <?=e($user['email'])?></p><p><b>Телефон:</b> <?=e($user['phone'])?></p><p><b>Логин:</b> <?=e($user['login'])?></p></div></div>
<?php include __DIR__.'/includes/footer.php'; ?>
