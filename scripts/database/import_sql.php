<?php
/**
 * Script import SQL cho chức năng Dine-in
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "📥 Bắt đầu import SQL cho chức năng Dine-in...\n\n";

    // Đọc file SQL
    $sqlFile = __DIR__ . '/create_dine_in_tables.sql';
    if (!file_exists($sqlFile)) {
        echo "❌ File SQL không tồn tại: $sqlFile\n";
        exit(1);
    }

    $sql = file_get_contents($sqlFile);

    // Tách các câu lệnh SQL
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "✅ Thực thi: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                // Bỏ qua lỗi nếu bảng đã tồn tại
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    echo "⚠️  Bảng đã tồn tại, bỏ qua\n";
                } else {
                    echo "❌ Lỗi: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    echo "\n🎉 Import SQL hoàn thành!\n";

} catch (PDOException $e) {
    echo "❌ Lỗi database: " . $e->getMessage() . "\n";
    exit(1);
}
?>
