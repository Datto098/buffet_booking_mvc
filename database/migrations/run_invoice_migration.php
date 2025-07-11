<?php
// Script để chạy migration hóa đơn
require_once __DIR__ . '/../../config/database.php';

try {
    // Kết nối database
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Đang chạy migration hóa đơn...\n";

    // Đọc file SQL
    $sql = file_get_contents(__DIR__ . '/create_invoice_system.sql');

    // Chia thành các câu lệnh riêng biệt
    $statements = explode(';', $sql);

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            echo "Executing: " . substr($statement, 0, 50) . "...\n";
            $conn->exec($statement);
        }
    }

    echo "Migration hoàn thành thành công!\n";

    // Kiểm tra các bảng đã được tạo
    $tables = ['buffet_pricing', 'invoices', 'additional_charge_types'];
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Bảng $table đã được tạo\n";
        } else {
            echo "✗ Bảng $table chưa được tạo\n";
        }
    }

} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>
