<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/queries.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/mail.php';

requireAuth();
$user = currentUser();
$cart = cartItems();
if (!$cart) { setFlash('warning', 'Нельзя оформить пустую корзину.'); redirect('cart.php'); }

$books = fetchBooksByIds(array_keys($cart));
$items = []; $total = 0;
foreach ($cart as $id => $qty) {
    if (!isset($books[$id])) continue;
    $qty = max(1, (int)$qty);
    $price = (float)$books[$id]['price'];
    $items[] = ['book_id' => (int)$id, 'quantity' => $qty, 'price' => $price];
    $total += $price * $qty;
}
if (!$items) { setFlash('danger', 'В корзине нет доступных товаров для оформления.'); redirect('cart.php'); }

$pickupPoints = ['Москва, ул. Книжная, 10', 'Москва, пр-т Мира, 21', 'Москва, ул. Тверская, 7'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $delivery_method = $_POST['delivery_method'] ?? 'pickup';
    $pickup_point = trim($_POST['pickup_point'] ?? '');
    $delivery_address = trim($_POST['delivery_address'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($full_name === '' || $phone === '' || $email === '') setFlash('danger', 'Заполните обязательные поля.');
    elseif (!validateEmail($email)) setFlash('danger', 'Некорректный email.');
    elseif (!validatePhone($phone)) setFlash('danger', 'Некорректный телефон.');
    elseif ($delivery_method === 'delivery' && $delivery_address === '') setFlash('danger', 'Укажите адрес для доставки.');
    elseif ($delivery_method === 'pickup' && $pickup_point === '') setFlash('danger', 'Выберите адрес самовывоза.');
    else {
        $orderId = createOrder([
            'user_id' => (int)$user['id'], 'full_name' => $full_name, 'phone' => $phone, 'email' => $email,
            'delivery_method' => $delivery_method, 'pickup_point' => $pickup_point, 'delivery_address' => $delivery_address,
            'comment' => $comment, 'total_amount' => $total,
        ], $items);
        if ($orderId) {
            clearCart();
            $order = fetchOrderById($orderId, (int)$user['id'], false);
            sendOrderMailToAdmin($order);
            setFlash('success', 'Заказ успешно создан. Номер заказа: ' . $order['order_number']);
            redirect('order_details.php?id=' . $orderId);
        }
        setFlash('danger', 'Не удалось оформить заказ. Проверьте остатки товаров.');
    }
}

$pageTitle = 'Оформление заказа'; include __DIR__ . '/includes/header.php'; ?>
<div class="container py-4"><h1>Оформление заказа</h1><form method="post" class="row g-3 bg-white p-3 rounded shadow-sm"><div class="col-md-4"><input class="form-control" name="full_name" required value="<?=e($user['full_name'])?>" placeholder="ФИО"></div><div class="col-md-4"><input class="form-control" name="phone" required value="<?=e($user['phone'])?>" placeholder="Телефон"></div><div class="col-md-4"><input class="form-control" type="email" name="email" required value="<?=e($user['email'])?>" placeholder="Email"></div><div class="col-md-4"><select class="form-select" name="delivery_method"><option value="pickup">Самовывоз</option><option value="delivery">Доставка</option></select></div><div class="col-md-8"><select class="form-select" name="pickup_point"><option value="">Выберите пункт самовывоза</option><?php foreach($pickupPoints as $p): ?><option value="<?=e($p)?>"><?=e($p)?></option><?php endforeach; ?></select></div><div class="col-12"><input class="form-control" name="delivery_address" placeholder="Адрес доставки"></div><div class="col-12"><textarea class="form-control" name="comment" placeholder="Комментарий"></textarea></div><div class="col-12"><h5>Итого: <?=formatPrice((float)$total)?></h5><button class="btn btn-primary">Подтвердить заказ</button></div></form></div>
<?php include __DIR__.'/includes/footer.php'; ?>
