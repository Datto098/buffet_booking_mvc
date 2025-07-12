<?php
// Script để chạy migration hóa đơn từng phần
require_once __DIR__ . '/../../config/database.php';

try {
    // Kết nối database
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Đang chạy migration hóa đơn từng bước...\n\n";

    // Danh sách các file migration
    $migrationFiles = [
        'create_invoice_system_1.sql',
        'create_invoice_system_2.sql',
        'create_invoice_system_3.sql',
        'create_invoice_system_4.sql',
        'create_invoice_system_5.sql'
    ];

    foreach ($migrationFiles as $file) {
        echo "Đang chạy: $file\n";

        $sql = file_get_contents(__DIR__ . '/' . $file);

        // Chia thành các câu lệnh riêng biệt
        $statements = explode(';', $sql);

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $conn->exec($statement);
                    echo "  ✓ " . substr($statement, 0, 60) . "...\n";
                } catch (PDOException $e) {
                    echo "  ✗ Lỗi: " . $e->getMessage() . "\n";
                }
            }
        }
        echo "\n";
    }

    echo "Migration hoàn thành!\n\n";

    // Kiểm tra các bảng đã được tạo
    echo "Kiểm tra các bảng:\n";
    $tables = ['buffet_pricing', 'invoices', 'additional_charge_types'];
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Bảng $table đã được tạo\n";

            // Đếm số dòng dữ liệu
            $countStmt = $conn->query("SELECT COUNT(*) as count FROM $table");
            $count = $countStmt->fetch()['count'];
            echo "  → Có $count dòng dữ liệu\n";
        } else {
            echo "✗ Bảng $table chưa được tạo\n";
        }
    }

    echo "\n=== Chi tiết dữ liệu ===\n";

    // Hiển thị dữ liệu buffet pricing
    echo "\nGiá buffet:\n";
    $stmt = $conn->query("SELECT * FROM buffet_pricing ORDER BY type, age_min");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['type']}: {$row['age_min']}-{$row['age_max']} tuổi = " . number_format($row['price']) . "đ\n";
    }

    // Hiển thị các loại phí phát sinh
    echo "\nCác loại phí phát sinh:\n";
    $stmt = $conn->query("SELECT * FROM additional_charge_types ORDER BY sort_order");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['name']}: " . number_format($row['default_price']) . "đ\n";
    }

} catch (PDOException $e) {
    echo "Lỗi kết nối: " . $e->getMessage() . "\n";
}
?>
