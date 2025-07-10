<?php
/**
 * Script thêm dữ liệu mẫu cho bảng tables
 */

require_once __DIR__ . '/../../config/database.php';

// Khởi tạo kết nối database
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    echo "✓ Kết nối database thành công\n";
} catch (Exception $e) {
    die("❌ Lỗi kết nối database: " . $e->getMessage() . "\n");
}

try {
    echo "Đang thêm dữ liệu mẫu cho bảng tables...\n";

    // Kiểm tra xem đã có dữ liệu chưa
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tables");
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "ℹ Bảng tables đã có dữ liệu. Bỏ qua thêm dữ liệu mẫu.\n";
    } else {
        // Thêm dữ liệu mẫu cho bảng tables
        $sampleTables = [
            ['table_number' => 'A1', 'capacity' => 4, 'location' => 'Tầng 1', 'description' => 'Bàn góc cửa sổ'],
            ['table_number' => 'A2', 'capacity' => 4, 'location' => 'Tầng 1', 'description' => 'Bàn giữa'],
            ['table_number' => 'A3', 'capacity' => 6, 'location' => 'Tầng 1', 'description' => 'Bàn lớn'],
            ['table_number' => 'B1', 'capacity' => 2, 'location' => 'Tầng 2', 'description' => 'Bàn đôi'],
            ['table_number' => 'B2', 'capacity' => 4, 'location' => 'Tầng 2', 'description' => 'Bàn gia đình'],
            ['table_number' => 'B3', 'capacity' => 8, 'location' => 'Tầng 2', 'description' => 'Bàn tiệc'],
            ['table_number' => 'C1', 'capacity' => 4, 'location' => 'Sân thượng', 'description' => 'Bàn ngoài trời'],
            ['table_number' => 'C2', 'capacity' => 6, 'location' => 'Sân thượng', 'description' => 'Bàn BBQ'],
            ['table_number' => 'D1', 'capacity' => 4, 'location' => 'Tầng 1', 'description' => 'Bàn VIP'],
            ['table_number' => 'D2', 'capacity' => 6, 'location' => 'Tầng 1', 'description' => 'Bàn VIP lớn']
        ];

        $sql = "INSERT INTO tables (table_number, capacity, location, description, is_available, status, created_at, updated_at)
                VALUES (:table_number, :capacity, :location, :description, 1, 'available', NOW(), NOW())";

        $stmt = $pdo->prepare($sql);

        foreach ($sampleTables as $table) {
            $stmt->execute([
                ':table_number' => $table['table_number'],
                ':capacity' => $table['capacity'],
                ':location' => $table['location'],
                ':description' => $table['description']
            ]);
            echo "✓ Đã thêm bàn {$table['table_number']}\n";
        }

        echo "\n🎉 Hoàn thành! Đã thêm " . count($sampleTables) . " bàn mẫu.\n";
    }

    // Hiển thị danh sách bàn hiện có
    echo "\n📋 Danh sách bàn hiện có:\n";
    $result = $pdo->query("SELECT id, table_number, capacity, location, status FROM tables ORDER BY table_number");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- ID: {$row['id']}, Bàn: {$row['table_number']}, Sức chứa: {$row['capacity']}, Vị trí: {$row['location']}, Trạng thái: {$row['status']}\n";
    }

} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
}
?>
