CREATE DATABASE IF NOT EXISTS bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookstore;

DROP TABLE IF EXISTS order_messages;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS order_statuses;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS roles;
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
  avatar VARCHAR(255) NOT NULL DEFAULT 'assets/img/avatars/default-avatar.svg',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE
  name VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE books (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(120) NOT NULL,
  publisher VARCHAR(120) NOT NULL DEFAULT '',
  publish_year SMALLINT UNSIGNED NOT NULL DEFAULT 2024,
  binding_type VARCHAR(80) NOT NULL DEFAULT 'Твердый',
  paper_type VARCHAR(80) NOT NULL DEFAULT 'Офсет',
  language VARCHAR(80) NOT NULL DEFAULT 'Русский',
  price DECIMAL(10,2) NOT NULL,
  discount_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
  quantity INT UNSIGNED NOT NULL DEFAULT 0,
  is_pickup_available TINYINT(1) NOT NULL DEFAULT 1,
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
  is_coming_soon TINYINT(1) NOT NULL DEFAULT 0,
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
  pickup_point VARCHAR(255) NULL,
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
  quantity INT UNSIGNED NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  INDEX idx_order_items_order (order_id),
  quantity INT UNSIGNED NOT NULL CHECK (quantity > 0),
  price DECIMAL(10,2) NOT NULL,
  INDEX idx_order_items_order (order_id),
  INDEX idx_order_items_book (book_id),
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_book FOREIGN KEY (book_id) REFERENCES books(id)
);

CREATE TABLE order_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_msg_order (order_id),
  CONSTRAINT fk_msg_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_msg_user FOREIGN KEY (user_id) REFERENCES users(id)
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
  rating TINYINT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO roles (name) VALUES ('admin'), ('user');
INSERT INTO order_statuses (name) VALUES ('новый'), ('в обработке'), ('готов к выдаче'), ('завершён');
INSERT INTO categories (name) VALUES
('Книги'),('Художественная литература'),('Литература Казахстана'),('Детская литература'),('Образование'),('Учебники'),('Популярная психология'),('Деловая литература'),('Дом. Семья. Досуг'),('Эзотерика. Астрология и нумерология'),('Книги на иностранных языках'),('Журналы и газеты'),('Медицина и здоровье'),('Наука'),('Публицистика'),('Подарочные издания'),('Компьютерная литература'),('Юридическая литература'),('Техническая литература'),('Искусство. Культура'),('Спорт. Туризм. Хобби'),('Энциклопедии. Справочники'),('Автомобили'),('Тайны, сенсации, катастрофы'),('Аксессуары, календари, открытки'),('Аудиокниги'),('Уцененные книги'),('Детективы');

INSERT INTO users (role_id,full_name,email,phone,login,password_hash,avatar) VALUES
(1,'Администратор','admin@listpero.local','+79991234567','admin','$2y$12$Ogvy.FdMX.tcADjnSG.G8.0tr8eGOrdhX2a8TLDRIv.5PWcnKeZUC','assets/img/avatars/default-avatar.svg'),
(2,'Тестовый пользователь','user@listpero.local','+79997654321','user','$2y$12$Z5Mss1csyYcw5Qa4va3HJeWgU3MUCW9tQGs3z3AuuLFm0S8vNZmUi','assets/img/avatars/default-avatar.svg');

INSERT INTO books (category_id,title,author,publisher,publish_year,binding_type,paper_type,language,price,discount_percent,quantity,is_pickup_available,short_description,full_description,image,is_new,is_popular,is_recommended,is_coming_soon) VALUES
(2,'Преступление и наказание','Ф. М. Достоевский','Эксмо',2024,'Твердый','Офсет','Русский',799,10,20,1,'Классический роман','Полное описание романа','https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=500&q=80',1,1,1,0),
(2,'Война и мир','Л. Н. Толстой','АСТ',2023,'Твердый','Офсет','Русский',920,0,12,1,'Эпопея о судьбах','Полное описание книги','https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=500&q=80',0,1,1,0),
(28,'Убийство в Восточном экспрессе','А. Кристи','Азбука',2022,'Мягкий','Офсет','Русский',560,15,18,1,'Классический детектив','Полное описание книги','https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=500&q=80',1,1,0,0),
(8,'Богатый папа, бедный папа','Роберт Кийосаки','Попурри',2022,'Мягкий','Офсет','Русский',650,5,30,1,'Финансовая грамотность','Полное описание книги','https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=500&q=80',0,1,0,0),
(7,'Думай медленно... решай быстро','Д. Канеман','МИФ',2021,'Твердый','Офсет','Русский',850,0,12,1,'О когнитивных искажениях','Полное описание книги','https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=500&q=80',0,1,1,0),
(17,'Чистый код','Р. Мартин','Питер',2020,'Твердый','Офсет','Русский',1400,20,25,1,'Практика разработки','Полное описание','https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=500&q=80',1,1,1,0),
(22,'Большая энциклопедия мира','Коллектив авторов','АСТ',2024,'Твердый','Мелованная','Русский',1900,0,8,1,'Энциклопедия','Полное описание','https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?auto=format&fit=crop&w=500&q=80',1,0,1,0),
(4,'Сказки для малышей','Корней Чуковский','Росмэн',2023,'Твердый','Мелованная','Русский',490,10,40,1,'Сборник сказок','Полное описание','https://images.unsplash.com/photo-1513001900722-370f803f498d?auto=format&fit=crop&w=500&q=80',1,1,1,0),
(11,'English Stories B1','John Smith','Oxford',2022,'Мягкий','Офсет','English',1100,0,15,1,'Рассказы на английском','Полное описание','https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=500&q=80',0,1,0,0),
(13,'Анатомия человека','Иванов И.И.','Медицина',2021,'Твердый','Офсет','Русский',1700,0,5,1,'Учебник по медицине','Полное описание','https://images.unsplash.com/photo-1532187643603-ba119ca4109e?auto=format&fit=crop&w=500&q=80',0,0,1,0),
(19,'Сопротивление материалов','Петров П.П.','Техлит',2019,'Твердый','Офсет','Русский',1200,25,7,1,'Технический учебник','Полное описание','https://images.unsplash.com/photo-1474932430478-367dbb6832c1?auto=format&fit=crop&w=500&q=80',0,0,0,0),
(8,'Атлант расправил плечи','Айн Рэнд','Альпина',2024,'Твердый','Офсет','Русский',1300,30,0,0,'Роман о свободе','Полное описание','https://images.unsplash.com/photo-1473755504818-b72b6dfdc226?auto=format&fit=crop&w=500&q=80',1,1,1,1),
(28,'Новая книга С. Кинга','С. Кинг','АСТ',2025,'Твердый','Офсет','Русский',990,0,0,0,'Скоро в продаже','Полное описание','https://images.unsplash.com/photo-1476275466078-4007374efbbe?auto=format&fit=crop&w=500&q=80',1,1,1,1),
(3,'Современный Казахстан','А. Нурпеисов','КазЛит',2023,'Твердый','Офсет','Русский',780,0,11,1,'История и культура Казахстана','Полное описание','https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=500&q=80',0,0,1,0),
(6,'Алгебра 10 класс','Мордкович','Просвещение',2022,'Мягкий','Офсет','Русский',450,0,35,1,'Школьный учебник','Полное описание','https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=500&q=80',0,1,0,0);
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
('Марина','Хороший выбор литературы, буду заказывать ещё.',5),
('Кирилл','Понравилась функция желаемого и фильтры.',5);
('Марина','Хороший выбор литературы, буду заказывать ещё.',5);
