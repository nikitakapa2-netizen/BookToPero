<?php
require_once __DIR__ . '/queries.php';
require_once __DIR__ . '/functions.php';

function currentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return fetchUserById((int)$_SESSION['user_id']);
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isAdmin(): bool
{
    $user = currentUser();
    return $user && $user['role_name'] === 'admin';
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        setFlash('warning', 'Для выполнения действия войдите в аккаунт.');
        redirect('login.php');
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        setFlash('danger', 'Доступ только для администратора.');
        redirect('../login.php');
    }
}

function loginUser(array $user): void
{
    $_SESSION['user_id'] = (int)$user['id'];
}

function logoutUser(): void
{
    unset($_SESSION['user_id']);
}
