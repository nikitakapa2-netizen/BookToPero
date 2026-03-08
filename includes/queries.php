<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function fetchCategories(): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    return $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
}

function fetchFeaturedBooks(string $flag, int $limit = 6): array
{
    $map = ['is_new', 'is_popular', 'is_recommended', 'is_coming_soon'];
    if (!in_array($flag, $map, true)) return [];
    $pdo = getPDO();
    if (!$pdo) return [];

    $stmt = $pdo->prepare("SELECT b.*, c.name AS category_name FROM books b JOIN categories c ON c.id=b.category_id WHERE b.$flag=1 ORDER BY b.created_at DESC LIMIT :limit");
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
    return $stmt->execute(['full_name' => $name, 'email' => $email, 'message' => $message]);
}

function buildBookFilters(array $filters, array &$params): string
{
    $sql = ' WHERE 1=1 ';
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
        $params['search'] = '%' . trim($filters['search']) . '%';
    }
    if (!empty($filters['author'])) {
        $sql .= ' AND b.author LIKE :author';
        $params['author'] = '%' . trim($filters['author']) . '%';
    }
    if (($filters['price_min'] ?? '') !== '') {
        $sql .= ' AND b.price >= :price_min';
        $params['price_min'] = (float)$filters['price_min'];
    }
    if (($filters['price_max'] ?? '') !== '') {
        $sql .= ' AND b.price <= :price_max';
        $params['price_max'] = (float)$filters['price_max'];
    }
    if (!empty($filters['publisher'])) {
        $sql .= ' AND b.publisher LIKE :publisher';
        $params['publisher'] = '%' . trim($filters['publisher']) . '%';
    }
    if (!empty($filters['publish_year'])) {
        $sql .= ' AND b.publish_year = :publish_year';
        $params['publish_year'] = (int)$filters['publish_year'];
    }
    if (!empty($filters['binding_type'])) {
        $sql .= ' AND b.binding_type = :binding_type';
        $params['binding_type'] = $filters['binding_type'];
    }
    if (!empty($filters['paper_type'])) {
        $sql .= ' AND b.paper_type = :paper_type';
        $params['paper_type'] = $filters['paper_type'];
    }
    if (!empty($filters['language'])) {
        $sql .= ' AND b.language = :language';
        $params['language'] = $filters['language'];
    }

    foreach (['available', 'is_new', 'is_popular', 'in_stock', 'has_discount', 'is_coming_soon'] as $flag) {
        if (!empty($filters[$flag])) {
            if ($flag === 'available') $sql .= ' AND b.quantity > 0';
            elseif ($flag === 'in_stock') $sql .= ' AND b.is_pickup_available = 1';
            elseif ($flag === 'has_discount') $sql .= ' AND b.discount_percent > 0';
            else $sql .= " AND b.$flag = 1";
        }
    }
    return $sql;
}

function fetchBooks(array $filters = [], int $limit = 0, int $offset = 0): array
{
    $pdo = getPDO();
    if (!$pdo) return [];

    $params = [];
    $sql = 'SELECT b.*, c.name AS category_name FROM books b JOIN categories c ON c.id=b.category_id';
    $sql .= buildBookFilters($filters, $params);
        $params['search'] = '%' . $filters['search'] . '%';
    }

    $sortMap = [
        'new' => 'b.created_at DESC',
        'price_asc' => 'b.price ASC',
        'price_desc' => 'b.price DESC',
        'title' => 'b.title ASC',
    ];
    $sql .= ' ORDER BY ' . ($sortMap[$filters['sort'] ?? 'new'] ?? $sortMap['new']);

    if ($limit > 0) {
        $sql .= ' LIMIT :limit OFFSET :offset';
    }

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue(':' . $k, $v);
    if ($limit > 0) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

function countBooks(array $filters = []): int
{
    $pdo = getPDO();
    if (!$pdo) return 0;
    $params = [];
    $sql = 'SELECT COUNT(*) FROM books b';
    $sql .= buildBookFilters($filters, $params);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
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

function fetchSimilarBooks(int $bookId, int $categoryId, int $limit = 6): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    $stmt = $pdo->prepare('SELECT * FROM books WHERE category_id=:category_id AND id <> :book_id ORDER BY created_at DESC LIMIT :limit');
    $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetchRandomBooks(int $limit = 6): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    $stmt = $pdo->prepare('SELECT * FROM books ORDER BY RAND() LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
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
    $stmt = $pdo->prepare('INSERT INTO users (role_id, full_name, email, phone, login, password_hash, avatar) VALUES (2, :full_name, :email, :phone, :login, :password_hash, :avatar)');
    return $stmt->execute($data + ['avatar' => 'assets/img/avatars/default-avatar.svg']);
}

function updateUserProfile(int $userId, string $fullName, string $phone, ?string $avatar = null): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $sql = 'UPDATE users SET full_name=:full_name, phone=:phone';
    $params = ['full_name' => $fullName, 'phone' => $phone, 'id' => $userId];
    if ($avatar !== null) {
        $sql .= ', avatar=:avatar';
        $params['avatar'] = $avatar;
    }
    $sql .= ' WHERE id=:id';
    return $pdo->prepare($sql)->execute($params);
}

function updateUserPassword(int $userId, string $passwordHash): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('UPDATE users SET password_hash=:password_hash WHERE id=:id');
    return $stmt->execute(['password_hash' => $passwordHash, 'id' => $userId]);
    $stmt = $pdo->prepare('INSERT INTO users (role_id, full_name, email, phone, login, password_hash) VALUES (2, :full_name, :email, :phone, :login, :password_hash)');
    return $stmt->execute($data);
}

function createOrder(array $payload, array $items): ?int
{
    $pdo = getPDO();
    if (!$pdo || !$items) return null;
    if (!$pdo) return null;
    try {
        $pdo->beginTransaction();
        do {
            $orderNumber = generateOrderNumber();
            $check = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE order_number=:order_number');
            $check->execute(['order_number' => $orderNumber]);
        } while ((int)$check->fetchColumn() > 0);

        $stmt = $pdo->prepare('INSERT INTO orders (order_number, user_id, status_id, full_name, phone, email, delivery_method, pickup_point, delivery_address, comment, total_amount) VALUES (:order_number, :user_id, 1, :full_name, :phone, :email, :delivery_method, :pickup_point, :delivery_address, :comment, :total_amount)');
        $stmt = $pdo->prepare('INSERT INTO orders (order_number, user_id, status_id, full_name, phone, email, delivery_method, delivery_address, comment, total_amount) VALUES (:order_number, :user_id, 1, :full_name, :phone, :email, :delivery_method, :delivery_address, :comment, :total_amount)');
        $stmt->execute($payload + ['order_number' => $orderNumber]);
        $orderId = (int)$pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (:order_id, :book_id, :quantity, :price)');
        $stockReadStmt = $pdo->prepare('SELECT quantity FROM books WHERE id=:book_id FOR UPDATE');
        $stockUpdateStmt = $pdo->prepare('UPDATE books SET quantity=:quantity WHERE id=:book_id');

        foreach ($items as $item) {
            $stockReadStmt->execute(['book_id' => (int)$item['book_id']]);
            $currentQty = (int)$stockReadStmt->fetchColumn();
            if ($currentQty < (int)$item['quantity']) throw new RuntimeException('stock');
            $itemStmt->execute(['order_id' => $orderId] + $item);
            $stockUpdateStmt->execute(['book_id' => (int)$item['book_id'], 'quantity' => $currentQty - (int)$item['quantity']]);
        }

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

function fetchOrderMessages(int $orderId): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    $stmt = $pdo->prepare('SELECT m.*, u.login, u.full_name, r.name as role_name FROM order_messages m JOIN users u ON u.id=m.user_id JOIN roles r ON r.id=u.role_id WHERE m.order_id=:order_id ORDER BY m.created_at ASC');
    $stmt->execute(['order_id' => $orderId]);
    return $stmt->fetchAll();
}

function createOrderMessage(int $orderId, int $userId, string $message): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('INSERT INTO order_messages (order_id, user_id, message) VALUES (:order_id, :user_id, :message)');
    return $stmt->execute(['order_id' => $orderId, 'user_id' => $userId, 'message' => $message]);
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
    return $pdo->prepare('UPDATE orders SET status_id=:status_id WHERE id=:id')->execute(['id' => $orderId, 'status_id' => $statusId]);
    $stmt = $pdo->prepare('UPDATE orders SET status_id=:status_id WHERE id=:id');
    return $stmt->execute(['id' => $orderId, 'status_id' => $statusId]);
}

function fetchUsers(): array
{
    $pdo = getPDO();
    if (!$pdo) return [];
    return $pdo->query('SELECT u.id,u.full_name,u.email,u.phone,u.login,u.avatar,u.created_at,r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.created_at DESC')->fetchAll();
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
    $stmt = $pdo->prepare('INSERT INTO books (category_id,title,author,publisher,publish_year,binding_type,paper_type,language,price,discount_percent,quantity,is_pickup_available,short_description,full_description,image,is_new,is_popular,is_recommended,is_coming_soon) VALUES (:category_id,:title,:author,:publisher,:publish_year,:binding_type,:paper_type,:language,:price,:discount_percent,:quantity,:is_pickup_available,:short_description,:full_description,:image,:is_new,:is_popular,:is_recommended,:is_coming_soon)');
    $stmt = $pdo->prepare('INSERT INTO books (category_id,title,author,price,quantity,short_description,full_description,image,is_new,is_popular,is_recommended) VALUES (:category_id,:title,:author,:price,:quantity,:short_description,:full_description,:image,:is_new,:is_popular,:is_recommended)');
    return $stmt->execute($data);
}

function updateBook(int $id, array $data): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('UPDATE books SET category_id=:category_id,title=:title,author=:author,publisher=:publisher,publish_year=:publish_year,binding_type=:binding_type,paper_type=:paper_type,language=:language,price=:price,discount_percent=:discount_percent,quantity=:quantity,is_pickup_available=:is_pickup_available,short_description=:short_description,full_description=:full_description,image=:image,is_new=:is_new,is_popular=:is_popular,is_recommended=:is_recommended,is_coming_soon=:is_coming_soon WHERE id=:id');
    $stmt = $pdo->prepare('UPDATE books SET category_id=:category_id,title=:title,author=:author,price=:price,quantity=:quantity,short_description=:short_description,full_description=:full_description,image=:image,is_new=:is_new,is_popular=:is_popular,is_recommended=:is_recommended WHERE id=:id');
    return $stmt->execute($data + ['id' => $id]);
}

function deleteBook(int $id): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    return $pdo->prepare('DELETE FROM books WHERE id=:id')->execute(['id' => $id]);
    $stmt = $pdo->prepare('DELETE FROM books WHERE id=:id');
    return $stmt->execute(['id' => $id]);
}

function createCategory(string $name): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    return $pdo->prepare('INSERT INTO categories (name) VALUES (:name)')->execute(['name' => $name]);
    $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
    return $stmt->execute(['name' => $name]);
}

function deleteCategory(int $id): bool
{
    $pdo = getPDO();
    if (!$pdo) return false;
    return $pdo->prepare('DELETE FROM categories WHERE id=:id')->execute(['id' => $id]);
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id=:id');
    return $stmt->execute(['id' => $id]);
}
