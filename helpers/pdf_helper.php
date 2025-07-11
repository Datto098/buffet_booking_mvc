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

function generateInvoicePdf($invoice, $order)
{
    // Tạo nội dung HTML cho hóa đơn
    $html = generateInvoiceHTML($invoice, $order);

    // Tạo PDF bằng Mpdf
    $mpdf = new Mpdf(['utf-8', 'A4']);
    $mpdf->WriteHTML($html);

    // Headers để download PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="invoice_' . $invoice['invoice_number'] . '.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    // Output PDF
    echo $mpdf->Output('', 'S');
    exit;
}

function generateInvoiceHTML($invoice, $order)
{
    $additionalCharges = json_decode($invoice['additional_charges'], true) ?? [];

    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Hóa đơn ' . $invoice['invoice_number'] . '</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            .header { text-align: center; margin-bottom: 20px; }
            .company-name { font-size: 18px; font-weight: bold; }
            .invoice-title { font-size: 16px; font-weight: bold; margin: 15px 0; }
            .info-table { width: 100%; margin-bottom: 15px; }
            .info-table td { padding: 5px; border: none; }
            .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
            .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            .items-table th { background-color: #f2f2f2; }
            .total-section { text-align: right; margin-top: 20px; }
            .total-row { margin: 5px 0; }
            .grand-total { font-weight: bold; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="company-name">NHÀ HÀNG BUFFET ABC</div>
            <div>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</div>
            <div>Điện thoại: 0123 456 789 | Email: info@buffetabc.com</div>
        </div>

        <div class="invoice-title">HÓA ĐƠN THANH TOÁN</div>

        <table class="info-table">
            <tr>
                <td><strong>Số hóa đơn:</strong> ' . $invoice['invoice_number'] . '</td>
                <td><strong>Ngày tạo:</strong> ' . date('d/m/Y H:i', strtotime($invoice['created_at'])) . '</td>
            </tr>
            <tr>
                <td><strong>Bàn số:</strong> ' . ($order['table_number'] ?? 'N/A') . '</td>
                <td><strong>Trạng thái:</strong> ' . getInvoicePaymentStatusText($invoice['payment_status']) . '</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Mục</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>';

    // Buffet charges
    if ($invoice['adult_count'] > 0) {
        $html .= '
                <tr>
                    <td>Buffet người lớn</td>
                    <td>' . $invoice['adult_count'] . '</td>
                    <td>' . number_format($invoice['adult_price']) . 'đ</td>
                    <td>' . number_format($invoice['adult_price'] * $invoice['adult_count']) . 'đ</td>
                </tr>';
    }

    if ($invoice['child_count'] > 0) {
        $html .= '
                <tr>
                    <td>Buffet trẻ em</td>
                    <td>' . $invoice['child_count'] . '</td>
                    <td>' . number_format($invoice['child_price']) . 'đ</td>
                    <td>' . number_format($invoice['child_price'] * $invoice['child_count']) . 'đ</td>
                </tr>';
    }

    // Food orders
    if ($invoice['food_total'] > 0) {
        $html .= '
                <tr>
                    <td>Đồ ăn thêm</td>
                    <td>1</td>
                    <td>' . number_format($invoice['food_total']) . 'đ</td>
                    <td>' . number_format($invoice['food_total']) . 'đ</td>
                </tr>';
    }

    // Additional charges
    foreach ($additionalCharges as $charge) {
        $html .= '
                <tr>
                    <td>' . $charge['name'] . '</td>
                    <td>' . $charge['quantity'] . ' ' . $charge['unit'] . '</td>
                    <td>' . number_format($charge['price']) . 'đ</td>
                    <td>' . number_format($charge['amount']) . 'đ</td>
                </tr>';
    }

    $html .= '
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">Tạm tính: ' . number_format($invoice['subtotal']) . 'đ</div>';

    if ($invoice['tax_amount'] > 0) {
        $html .= '<div class="total-row">Thuế (' . $invoice['tax_rate'] . '%): ' . number_format($invoice['tax_amount']) . 'đ</div>';
    }

    $html .= '
            <div class="total-row grand-total">Tổng cộng: ' . number_format($invoice['total_amount']) . 'đ</div>
            <div class="total-row">Hình thức thanh toán: ' . getInvoicePaymentMethodText($invoice['payment_method']) . '</div>
        </div>';

    if (!empty($invoice['notes'])) {
        $html .= '
        <div style="margin-top: 20px;">
            <strong>Ghi chú:</strong> ' . nl2br(htmlspecialchars($invoice['notes'])) . '
        </div>';
    }

    $html .= '
        <div style="margin-top: 30px; text-align: center; font-style: italic;">
            Cảm ơn quý khách đã sử dụng dịch vụ!
        </div>
    </body>
    </html>';

    return $html;
}

function getInvoicePaymentStatusText($status)
{
    switch ($status) {
        case 'pending': return 'Chờ thanh toán';
        case 'paid': return 'Đã thanh toán';
        case 'cancelled': return 'Đã hủy';
        default: return 'Không xác định';
    }
}

function getInvoicePaymentMethodText($method)
{
    switch ($method) {
        case 'cash': return 'Tiền mặt';
        case 'card': return 'Thẻ tín dụng';
        case 'bank_transfer': return 'Chuyển khoản';
        case 'vnpay': return 'VNPay';
        default: return $method ?: 'Tiền mặt';
    }
}
