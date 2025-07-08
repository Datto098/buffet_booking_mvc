<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking #<?= $booking['id'] ?? '' ?></title>
</head>
<body style="font-family: Arial, sans-serif; background: #f6f7fb; margin:0; padding:0;">
    <div style="max-width:600px;margin:32px auto;background:#fff;border-radius:16px;box-shadow:0 4px 24px #0002;padding:40px 32px;">
        <h2 style="color:#222;margin-top:0;margin-bottom:8px;font-size:2rem;letter-spacing:1px;">
            Booking #<?= $booking['id'] ?? '' ?>
        </h2>
        <div style="color:#888;font-size:0.98rem;margin-bottom:24px;">
            <?php date_default_timezone_set('Asia/Ho_Chi_Minh'); ?>
            Created on <?= !empty($booking['created_at']) ? date('F d, Y \a\t h:i A', strtotime($booking['created_at'])) : date('F d, Y \a\t h:i A') ?>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:1.08rem;">
            <tr>
                <td style="padding:8px 0;width:160px;"><b>Khách hàng:</b></td>
                <td style="padding:8px 0;"><?= htmlspecialchars($booking['customer_name'] ?? '') ?></td>
            </tr>
            <tr>
                <td style="padding:8px 0;"><b>Email:</b></td>
                <td style="padding:8px 0;"><?= htmlspecialchars($booking['customer_email'] ?? '') ?></td>
            </tr>
            <tr>
                <td style="padding:8px 0;"><b>Điện thoại:</b></td>
                <td style="padding:8px 0;"><?= htmlspecialchars($booking['customer_phone'] ?? '') ?></td>
            </tr>
            <tr>
                <td style="padding:8px 0;"><b>Ngày:</b></td>
                <td style="padding:8px 0;">
                    <?php
                    if (!empty($booking['booking_date'])) {
                        echo date('d/m/Y', strtotime($booking['booking_date']));
                    } elseif (!empty($booking['booking_datetime'])) {
                        echo date('d/m/Y', strtotime($booking['booking_datetime']));
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="padding:8px 0;"><b>Giờ:</b></td>
                <td style="padding:8px 0;">
                    <?php
                    if (!empty($booking['booking_time'])) {
                        echo date('H:i', strtotime($booking['booking_time']));
                    } elseif (!empty($booking['booking_datetime'])) {
                        echo date('H:i', strtotime($booking['booking_datetime']));
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="padding:8px 0;"><b>Số lượng khách:</b></td>
                <td style="padding:8px 0;"><?= $booking['guest_count'] ?? ($booking['party_size'] ?? '') ?></td>
            </tr>
            <?php if (!empty($booking['special_requests'])): ?>
            <tr>
                <td style="padding:8px 0;vertical-align:top;"><b>Yêu cầu đặc biệt:</b></td>
                <td style="padding:8px 0;"><?= nl2br(htmlspecialchars($booking['special_requests'])) ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <hr style="margin:32px 0 24px 0;border:0;border-top:1px solid #eee;">
        <div style="margin-bottom:24px;">
            <span style="font-size:1.1rem;font-weight:bold;">Trạng thái:</span>
            <?php
                $status = strtolower($booking['status'] ?? 'pending');
                if ($status === 'pending') {
                    echo '<span style="display:inline-block;margin-left:12px;padding:8px 22px;border-radius:18px;background:#ffe066;color:#222;font-weight:bold;font-size:1rem;border:1px solid #ffd700;">Đang xác nhận</span>';
                } elseif ($status === 'confirmed') {
                    echo '<span style="display:inline-block;margin-left:12px;padding:8px 22px;border-radius:18px;background:#51cf66;color:#fff;font-weight:bold;font-size:1rem;border:1px solid #28a745;">Đã xác nhận</span>';
                } elseif ($status === 'cancelled') {
                    echo '<span style="display:inline-block;margin-left:12px;padding:8px 22px;border-radius:18px;background:#ff6b6b;color:#fff;font-weight:bold;font-size:1rem;border:1px solid #dc3545;">Đã hủy</span>';
                } else {
                    echo '<span style="display:inline-block;margin-left:12px;padding:8px 22px;border-radius:18px;background:#adb5bd;color:#fff;font-weight:bold;font-size:1rem;">'.ucfirst($status).'</span>';
                }
            ?>
        </div>
        <div style="border:1px solid #eee; border-radius:12px; padding:24px; background:#f8fafd;">
            <h4 style="margin:0 0 16px 0; color:#007bff;">Thông tin nhà hàng</h4>
            <div style="margin-bottom:12px;">
                <b>Địa chỉ:</b><br>
                123 Buffet Street<br>
                Food District, FD 12345<br>
                United States
            </div>
            <div style="margin-bottom:12px;">
                <b>Liên hệ:</b><br>
                (555) 123-4567<br>
                info@buffetbooking.com
            </div>
            <div style="margin-bottom:12px;">
                <b>Giờ mở cửa:</b><br>
                Thứ 2 - Thứ 5: 11:00 - 21:00<br>
                Thứ 6 - Thứ 7: 11:00 - 22:00<br>
                Chủ nhật: 12:00 - 20:00
            </div>
            <div>
                <b>Chính sách:</b>
                <ul style="margin:8px 0 0 18px; padding:0; color:#555;">
                    <li>Phải hủy trước ít nhất 2 giờ</li>
                    <li>Đến muộn (15 phút trở lên) có thể dẫn đến việc phải chỉ định lại bàn</li>
                    <li>Những trường hợp không đến sẽ được ghi chú trong lịch sử đặt chỗ của bạn</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>