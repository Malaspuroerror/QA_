<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;

    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress('johnpaulmanarang07@gmail.com', 'John Mana'); // send test to your Gmail address

    $mail->isHTML(true);
    $mail->Subject = 'Test email';
    $mail->Body = 'This is a test from the app.';

    $mail->send();
    echo 'OK: Email sent';
} catch (Exception $e) {
    echo 'ERROR: ' . $mail->ErrorInfo;
}