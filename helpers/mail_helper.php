<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendResetMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'euyry88@gmail.com';
        $mail->Password = 'gnkj jyti ydew ctdf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('euyry88@gmail.com', 'Buffet Booking');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Subject = $subject;
        $mail->Body    = $body;        $mail->send();
        return true;
    } catch (Exception $e) {
        // Ghi log hoặc xử lý lỗi
        error_log("Mail sending failed: " . $e->getMessage());
        return false;
    }
}
?>
