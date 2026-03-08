CREATE DATABASE IF NOT EXISTS bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookstore;

DROP TABLE IF EXISTS order_items; DROP TABLE IF EXISTS orders; DROP TABLE IF EXISTS reviews; DROP TABLE IF EXISTS contacts; DROP TABLE IF EXISTS books; DROP TABLE IF EXISTS users; DROP TABLE IF EXISTS order_statuses; DROP TABLE IF EXISTS categories; DROP TABLE IF EXISTS roles;

CREATE TABLE roles (
  id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id TINYINT UNSIGNED NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  phone VARCHAR(30) NOT NULL,
  login VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE books (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  author VARCHAR(120) NOT NULL,
  price DECIMAL(10,2) NOT NULL CHECK (price > 0),
  quantity INT UNSIGNED NOT NULL DEFAULT 0,
  short_description VARCHAR(255) NOT NULL,
  full_description TEXT NOT NULL,
  image VARCHAR(255) NOT NULL,
  is_new TINYINT(1) NOT NULL DEFAULT 0,
  is_popular TINYINT(1) NOT NULL DEFAULT 0,
  is_recommended TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_books_category (category_id),
  CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE order_statuses (
  id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number CHAR(8) NOT NULL UNIQUE,
  user_id INT UNSIGNED NOT NULL,
  status_id TINYINT UNSIGNED NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  email VARCHAR(120) NOT NULL,
  delivery_method ENUM('pickup','delivery') NOT NULL,
  delivery_address VARCHAR(255) NULL,
  comment TEXT NULL,
  total_amount DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_orders_user (user_id),
  INDEX idx_orders_status (status_id),
  INDEX idx_orders_number (order_number),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_orders_status FOREIGN KEY (status_id) REFERENCES order_statuses(id)
);

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  book_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL CHECK (quantity > 0),
  price DECIMAL(10,2) NOT NULL,
  INDEX idx_order_items_order (order_id),
  INDEX idx_order_items_book (book_id),
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_book FOREIGN KEY (book_id) REFERENCES books(id)
);

CREATE TABLE contacts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(120) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  review_text TEXT NOT NULL,
  rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO roles (name) VALUES ('admin'), ('user');
INSERT INTO order_statuses (name) VALUES ('новый'), ('в обработке'), ('готов к выдаче'), ('завершён');
INSERT INTO categories (name) VALUES ('классика'),('фантастика'),('психология'),('бизнес'),('детективы'),('саморазвитие'),('история'),('детская литература');
INSERT INTO users (role_id,full_name,email,phone,login,password_hash) VALUES
(1,'Администратор','admin@listpero.local','+79991234567','admin','$2y$12$Ogvy.FdMX.tcADjnSG.G8.0tr8eGOrdhX2a8TLDRIv.5PWcnKeZUC'),
(2,'Тестовый пользователь','user@listpero.local','+79997654321','user','$2y$12$Z5Mss1csyYcw5Qa4va3HJeWgU3MUCW9tQGs3z3AuuLFm0S8vNZmUi');

INSERT INTO books (category_id,title,author,price,quantity,short_description,full_description,image,is_new,is_popular,is_recommended) VALUES
(1,'Преступление и наказание','Ф. Достоевский',799.00,20,'Классический роман о совести и вине.','Полное описание романа о внутренней борьбе и ответственности.','https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=500&q=80',1,1,1),
(2,'Дюна','Фрэнк Герберт',990.00,15,'Эпическая научная фантастика.','Полное описание: борьба за Арракис, власть и пророчество.','https://images.unsplash.com/photo-1473755504818-b72b6dfdc226?auto=format&fit=crop&w=500&q=80',1,1,0),
(3,'Думай медленно... решай быстро','Даниэль Канеман',850.00,12,'О когнитивных искажениях.','Разбор двух систем мышления и принятия решений.','https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=500&q=80',0,1,1),
(4,'Богатый папа, бедный папа','Роберт Кийосаки',650.00,30,'Финансовая грамотность простыми словами.','Практические принципы управления личными финансами.','https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=500&q=80',0,1,0),
(5,'Шерлок Холмс','Артур Конан Дойл',700.00,18,'Сборник детективных рассказов.','Классические расследования гениального сыщика.','https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=500&q=80',1,0,1);

INSERT INTO reviews (full_name,review_text,rating) VALUES
('Елена','Отличный магазин, быстрая доставка и вежливый сервис.',5),
('Иван','Удобный каталог и приятный дизайн.',4),
('Марина','Хороший выбор литературы, буду заказывать ещё.',5);
