<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function getFlashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

function validateEmail(string $email): bool
{
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone(string $phone): bool
{
    return (bool)preg_match(PHONE_MASK_REGEX, $phone);
}

function cartItems(): array
{
    return $_SESSION['cart'] ?? [];
}

function cartCount(): int
{
    return array_sum(cartItems());
}

function addToCart(int $bookId, int $quantity = 1): void
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][$bookId] = ($_SESSION['cart'][$bookId] ?? 0) + max(1, $quantity);
}

function updateCartItem(int $bookId, int $quantity): void
{
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$bookId]);
        return;
    }
    $_SESSION['cart'][$bookId] = $quantity;
}

function removeCartItem(int $bookId): void
{
    unset($_SESSION['cart'][$bookId]);
}

function clearCart(): void
{
    $_SESSION['cart'] = [];
}

function wishlistItems(): array
{
    return $_SESSION['wishlist'] ?? [];
}

function wishlistCount(): int
{
    return count(wishlistItems());
}

function inWishlist(int $bookId): bool
{
    return in_array($bookId, wishlistItems(), true);
}

function toggleWishlist(int $bookId): void
{
    $items = wishlistItems();
    if (in_array($bookId, $items, true)) {
        $_SESSION['wishlist'] = array_values(array_filter($items, fn($id) => (int)$id !== $bookId));
    } else {
        $items[] = $bookId;
        $_SESSION['wishlist'] = array_values(array_unique(array_map('intval', $items)));
    }
}

function generateOrderNumber(): string
{
    return (string)random_int(10000000, 99999999);
}

function formatPrice(float $price): string
{
    return number_format($price, 2, '.', ' ') . ' ' . CURRENCY;
}

function deliveryLabel(string $method): string
{
    return $method === 'delivery' ? 'Доставка' : 'Самовывоз';
}

function isActivePage(string $file): bool
{
    return basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '') === $file;
}
