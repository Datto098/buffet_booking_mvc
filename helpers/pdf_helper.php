<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/mail_helper.php';

use Mpdf\Mpdf;
use PHPMailer\PHPMailer\PHPMailer;

function sendBookingPDFMail($customerEmail, $subject, $body, $htmlContent) {
    // Tạo PDF từ HTML
    $mpdf = new Mpdf(['utf-8', 'A4']);
    $mpdf->WriteHTML($htmlContent);
    $pdfContent = $mpdf->Output('', 'S');

    // Gửi email có đính kèm file PDF
    $mail = new PHPMailer(true);
    try {
        // Cấu hình SMTP giống mail_helper.php
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'euyry88@gmail.com';
        $mail->Password = 'gnkj jyti ydew ctdf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('euyry88@gmail.com', 'Buffet Booking');
        $mail->addAddress($customerEmail);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Đính kèm file PDF
        $mail->addStringAttachment($pdfContent, 'booking.pdf');

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}