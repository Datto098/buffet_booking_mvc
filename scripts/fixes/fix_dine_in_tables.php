<?php
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getInstance()->getConnection();

try {
    // Đọc file SQL
    $sql = file_get_contents(__DIR__ . '/../database/create_dine_in_tables.sql');

    // Thực thi từng câu lệnh SQL
    $statements = explode(';', $sql);

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
            echo "Executed: " . substr($statement, 0, 50) . "...\n";
        }
    }

    echo "✅ Bảng dine_in_orders đã được tạo thành công!\n";

} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
}
?>
