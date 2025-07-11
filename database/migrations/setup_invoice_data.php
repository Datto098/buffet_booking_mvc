<?php
// Script để cập nhật dữ liệu cho hệ thống hóa đơn hiện có
require_once __DIR__ . '/../../config/database.php';

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Cập nhật dữ liệu cho hệ thống hóa đơn ===\n\n";

    // 1. Xóa dữ liệu cũ trong buffet_pricing
    echo "1. Cập nhật giá buffet...\n";
    $conn->exec("DELETE FROM buffet_pricing");

    // Insert giá buffet mới
    $sql = "INSERT INTO buffet_pricing (type, age_from, age_to, price, description) VALUES
            ('adult', 18, 99, 299000.00, 'Giá buffet người lớn'),
            ('child', 11, 17, 199000.00, 'Giá buffet trẻ em 11-17 tuổi'),
            ('child', 6, 10, 99000.00, 'Giá buffet trẻ em 6-10 tuổi'),
            ('child', 0, 5, 0.00, 'Miễn phí cho trẻ em dưới 6 tuổi')";
    $conn->exec($sql);
    echo "   ✓ Đã cập nhật giá buffet\n";

    // 2. Xóa và cập nhật additional_charge_types
    echo "2. Cập nhật các loại phí phát sinh...\n";
    $conn->exec("DELETE FROM additional_charge_types");

    $sql = "INSERT INTO additional_charge_types (name, price, unit, description) VALUES
            ('Khăn ướt', 5000.00, 'cái', 'Phí khăn ướt cho khách'),
            ('Nước ngọt', 15000.00, 'chai', 'Nước ngọt các loại'),
            ('Bia', 25000.00, 'chai', 'Bia các loại'),
            ('Nước suối', 10000.00, 'chai', 'Nước suối đóng chai'),
            ('Phí vệ sinh', 20000.00, 'lần', 'Phí vệ sinh bàn ghế đặc biệt'),
            ('Phí đậu xe', 10000.00, 'lần', 'Phí đậu xe ô tô/xe máy')";
    $conn->exec($sql);
    echo "   ✓ Đã cập nhật các loại phí phát sinh\n";

    echo "\n=== Kết quả ===\n";

    // Hiển thị dữ liệu buffet pricing
    echo "\nGiá buffet hiện tại:\n";
    $stmt = $conn->query("SELECT * FROM buffet_pricing ORDER BY type, age_from");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ageRange = $row['age_from'] . '-' . $row['age_to'] . ' tuổi';
        echo "- {$row['type']}: $ageRange = " . number_format($row['price']) . "đ ({$row['description']})\n";
    }

    // Hiển thị các loại phí phát sinh
    echo "\nCác loại phí phát sinh:\n";
    $stmt = $conn->query("SELECT * FROM additional_charge_types ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['name']}: " . number_format($row['price']) . "đ/{$row['unit']} - {$row['description']}\n";
    }

    echo "\n✅ Hệ thống hóa đơn đã sẵn sàng!\n";

} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>
