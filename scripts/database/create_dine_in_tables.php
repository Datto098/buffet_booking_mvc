<?php
/**
 * Script tạo bảng cho chức năng đặt món tại bàn
 */

require_once __DIR__ . '/../../config/database.php';

try {
    // Lấy kết nối database
    $db = Database::getInstance()->getConnection();

    // Đọc nội dung file SQL
    $sql = file_get_contents(__DIR__ . '/create_dine_in_tables.sql');

    // Thực thi từng câu lệnh SQL
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->exec($statement);
        }
    }

    echo "Đã tạo bảng thành công!\n";
} catch (PDOException $e) {
    die("Lỗi: " . $e->getMessage() . "\n");
}
?>
