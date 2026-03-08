<?php
require_once __DIR__ . '/config.php';

function sendOrderMailToAdmin(array $order): bool
{
    $subject = 'Новый заказ №' . $order['order_number'];
    $lines = [
        'Номер: ' . $order['order_number'],
        'Дата: ' . $order['created_at'],
        'Клиент: ' . $order['full_name'],
        'Телефон: ' . $order['phone'],
        'Email: ' . $order['email'],
        'Способ: ' . $order['delivery_method'],
        'Адрес: ' . ($order['delivery_address'] ?: 'самовывоз'),
        'Комментарий: ' . ($order['comment'] ?: '-'),
        'Сумма: ' . $order['total_amount'],
        'Позиции:'
    ];
    foreach ($order['items'] as $item) {
        $lines[] = sprintf('- %s x%d (%s)', $item['title'], $item['quantity'], $item['price']);
    }
    $body = implode("\n", $lines);

    if (!SMTP_ENABLED) {
        // Безопасный fallback: логируем письмо локально, если SMTP не настроен.
        file_put_contents(__DIR__ . '/../storage/mail.log', "[$subject]\n$body\n\n", FILE_APPEND);
        return true;
    }

    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        file_put_contents(__DIR__ . '/../storage/mail.log', "[SMTP class missing][$subject]\n$body\n\n", FILE_APPEND);
        return false;
    }

    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->Port = SMTP_PORT;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress(ADMIN_EMAIL);
        $mail->Subject = $subject;
        $mail->Body = $body;
        return $mail->send();
    } catch (Throwable $e) {
        file_put_contents(__DIR__ . '/../storage/mail.log', "[SMTP error][$subject]\n$body\n\n", FILE_APPEND);
        return false;
    }
}
