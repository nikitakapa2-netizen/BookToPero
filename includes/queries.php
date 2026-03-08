<?php
require_once __DIR__ . '/db.php';

function fetchCategories(): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    return $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
}

function fetchFeaturedBooks(string $flag, int $limit = 6): array
{
    $allowed = ['is_new', 'is_popular', 'is_recommended'];
    if (!in_array($flag, $allowed, true)) return [];
    $pdo = getPDO();
    if (!$pdo) return [];
    $stmt = $pdo->prepare("SELECT b.*, c.name AS category_name FROM books b JOIN categories c ON c.id=b.category_id WHERE {$flag}=1 ORDER BY b.created_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetchReviews(int $limit = 8): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    $stmt = $pdo->prepare('SELECT * FROM reviews ORDER BY created_at DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function createContact(string $name, string $email, string $message): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('INSERT INTO contacts (full_name, email, message) VALUES (:full_name, :email, :message)');
    return $stmt->execute(compact('name', 'email', 'message') + ['full_name' => $name]);
}

function fetchBooks(array $filters = []): array
{
    $pdo = getPDO();
    if (!$pdo) return [];

    $sql = 'SELECT b.*, c.name AS category_name FROM books b JOIN categories c ON c.id=b.category_id WHERE 1=1';
    $params = [];

    if (!empty($filters['category_id'])) {
        $sql .= ' AND b.category_id = :category_id';
        $params['category_id'] = (int)$filters['category_id'];
    }
    if (!empty($filters['search'])) {
        $sql .= ' AND (b.title LIKE :search OR b.author LIKE :search)';
        $params['search'] = '%' . $filters['search'] . '%';
    }

    $sortMap = [
        'new' => 'b.created_at DESC',
        'price_asc' => 'b.price ASC',
        'price_desc' => 'b.price DESC',
        'title' => 'b.title ASC'
    ];
    $sql .= ' ORDER BY ' . ($sortMap[$filters['sort'] ?? 'new'] ?? $sortMap['new']);

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetchBookById(int $id): ?array
{
    $pdo = getPDO();
    if (!$pdo) return null;
    $stmt = $pdo->prepare('SELECT b.*, c.name AS category_name FROM books b JOIN categories c ON c.id=b.category_id WHERE b.id=:id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetch() ?: null;
}

function fetchBooksByIds(array $ids): array
{
    if (!$ids) return [];
    $pdo = getPDO();
    if (!$pdo) return [];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id IN ($placeholders)");
    $stmt->execute(array_values($ids));
    $books = [];
    foreach ($stmt->fetchAll() as $row) $books[(int)$row['id']] = $row;
    return $books;
}

function fetchUserById(int $id): ?array
{
    $pdo = getPDO();
    if (!$pdo) return null;
    $stmt = $pdo->prepare('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=:id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetch() ?: null;
}

function fetchUserByLogin(string $login): ?array
{
    $pdo = getPDO();
    if (!$pdo) return null;
    $stmt = $pdo->prepare('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.login=:login');
    $stmt->execute(['login' => $login]);
    return $stmt->fetch() ?: null;
}

function emailOrLoginExists(string $email, string $login): bool
{
    $pdo = getPDO();
    if (!$pdo) return true;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email=:email OR login=:login');
    $stmt->execute(['email' => $email, 'login' => $login]);
    return (int)$stmt->fetchColumn() > 0;
}

function createUser(array $data): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('INSERT INTO users (role_id, full_name, email, phone, login, password_hash) VALUES (2, :full_name, :email, :phone, :login, :password_hash)');
    return $stmt->execute($data);
}

function createOrder(array $payload, array $items): ?int
{
    $pdo = getPDO();
    if (!$pdo) return null;
    try {
        $pdo->beginTransaction();
        do {
            $orderNumber = generateOrderNumber();
            $check = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE order_number=:order_number');
            $check->execute(['order_number' => $orderNumber]);
        } while ((int)$check->fetchColumn() > 0);

        $stmt = $pdo->prepare('INSERT INTO orders (order_number, user_id, status_id, full_name, phone, email, delivery_method, delivery_address, comment, total_amount) VALUES (:order_number, :user_id, 1, :full_name, :phone, :email, :delivery_method, :delivery_address, :comment, :total_amount)');
        $stmt->execute($payload + ['order_number' => $orderNumber]);
        $orderId = (int)$pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (:order_id, :book_id, :quantity, :price)');
        $stockStmt = $pdo->prepare('UPDATE books SET quantity = quantity - :quantity WHERE id=:book_id AND quantity >= :quantity');

        foreach ($items as $item) {
            $itemStmt->execute(['order_id' => $orderId] + $item);
            $stockStmt->execute(['book_id' => $item['book_id'], 'quantity' => $item['quantity']]);
            if ($stockStmt->rowCount() === 0) {
                throw new RuntimeException('Недостаточно товара на складе');
            }
        }
        $pdo->commit();
        return $orderId;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return null;
    }
}

function fetchOrderById(int $orderId, int $userId = 0, bool $isAdmin = false): ?array
{
    $pdo = getPDO();
    if (!$pdo) return null;
    $sql = 'SELECT o.*, s.name AS status_name FROM orders o JOIN order_statuses s ON s.id=o.status_id WHERE o.id=:id';
    $params = ['id' => $orderId];
    if (!$isAdmin) {
        $sql .= ' AND o.user_id=:user_id';
        $params['user_id'] = $userId;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $order = $stmt->fetch();
    if (!$order) return null;

    $items = $pdo->prepare('SELECT oi.*, b.title, b.author FROM order_items oi JOIN books b ON b.id=oi.book_id WHERE oi.order_id=:id');
    $items->execute(['id' => $orderId]);
    $order['items'] = $items->fetchAll();
    return $order;
}

function fetchOrdersByUser(int $userId): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    $stmt = $pdo->prepare('SELECT o.*, s.name AS status_name FROM orders o JOIN order_statuses s ON s.id=o.status_id WHERE user_id=:user_id ORDER BY created_at DESC');
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
}

function fetchAllOrders(array $filters = []): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    $sql = 'SELECT o.*, u.login, s.name AS status_name FROM orders o JOIN users u ON u.id=o.user_id JOIN order_statuses s ON s.id=o.status_id WHERE 1=1';
    $params = [];
    if (!empty($filters['status_id'])) {
        $sql .= ' AND o.status_id=:status_id';
        $params['status_id'] = (int)$filters['status_id'];
    }
    if (!empty($filters['order_number'])) {
        $sql .= ' AND o.order_number LIKE :order_number';
        $params['order_number'] = '%' . $filters['order_number'] . '%';
    }
    $sql .= ' ORDER BY o.created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetchStatuses(): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    return $pdo->query('SELECT * FROM order_statuses ORDER BY id')->fetchAll();
}

function updateOrderStatus(int $orderId, int $statusId): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('UPDATE orders SET status_id=:status_id WHERE id=:id');
    return $stmt->execute(['id' => $orderId, 'status_id' => $statusId]);
}

function fetchUsers(): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    return $pdo->query('SELECT u.id,u.full_name,u.email,u.phone,u.login,u.created_at,r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.created_at DESC')->fetchAll();
}

function fetchContacts(): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    return $pdo->query('SELECT * FROM contacts ORDER BY created_at DESC')->fetchAll();
}

function createBook(array $data): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('INSERT INTO books (category_id,title,author,price,quantity,short_description,full_description,image,is_new,is_popular,is_recommended) VALUES (:category_id,:title,:author,:price,:quantity,:short_description,:full_description,:image,:is_new,:is_popular,:is_recommended)');
    return $stmt->execute($data);
}

function updateBook(int $id, array $data): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('UPDATE books SET category_id=:category_id,title=:title,author=:author,price=:price,quantity=:quantity,short_description=:short_description,full_description=:full_description,image=:image,is_new=:is_new,is_popular=:is_popular,is_recommended=:is_recommended WHERE id=:id');
    return $stmt->execute($data + ['id' => $id]);
}

function deleteBook(int $id): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('DELETE FROM books WHERE id=:id');
    return $stmt->execute(['id' => $id]);
}

function createCategory(string $name): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
    return $stmt->execute(['name' => $name]);
}

function deleteCategory(int $id): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id=:id');
    return $stmt->execute(['id' => $id]);
}
