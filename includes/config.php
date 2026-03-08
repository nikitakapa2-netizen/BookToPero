<?php
// Общая конфигурация приложения.
define('APP_NAME', 'Лист и Перо');
define('BASE_URL', '/BookToPero');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'bookstore');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'no-reply@example.com');
define('SMTP_PASS', 'change_me');
define('SMTP_FROM_EMAIL', 'no-reply@example.com');
define('SMTP_FROM_NAME', APP_NAME);
define('ADMIN_EMAIL', 'admin@example.com');
define('SMTP_ENABLED', false); // Установите true и заполните SMTP_* для отправки писем.

define('CURRENCY', '₽');
define('PHONE_MASK_REGEX', '/^\+?[0-9\-\s\(\)]{10,20}$/');
