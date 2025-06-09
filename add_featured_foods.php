<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "=== Adding more featured foods ===\n";

    // Thêm món ăn mới với thông tin chi tiết
    $foods = [
        [
            'name' => 'Bò Wagyu Nướng Đá Muối',
            'description' => 'Thịt bò Wagyu A5 cao cấp nướng trên đá muối Himalaya, mang đến hương vị tuyệt vời với độ mềm tan trong miệng.',
            'price' => 450000,
            'is_popular' => 1,
            'category_id' => 2
        ],
        [
            'name' => 'Tôm Hùm Alaska Nướng Bơ',
            'description' => 'Tôm hùm Alaska tươi ngon nướng với bơ thảo mộc và tỏi, phục vụ cùng rau củ nướng và sốt đặc biệt.',
            'price' => 650000,
            'is_new' => 1,
            'category_id' => 2
        ],
        [
            'name' => 'Sushi Premium Omakase',
            'description' => 'Set sushi cao cấp với cá ngừ đại dương, cá hồi Na Uy và các loại hải sản tươi sống được chef tuyển chọn.',
            'price' => 380000,
            'is_seasonal' => 1,
            'category_id' => 1
        ],
        [
            'name' => 'Bánh Tráng Nướng Đà Lạt',
            'description' => 'Đặc sản Đà Lạt với bánh tráng nướng giòn, trứng cút, khô bò và rau thơm tươi.',
            'price' => 85000,
            'is_popular' => 1,
            'category_id' => 1
        ],
        [
            'name' => 'Kem Gelato Ý Artisan',
            'description' => 'Kem gelato thủ công với các hương vị độc đáo: vani Madagascar, chocolate Belgian và pistachio Sicily.',
            'price' => 120000,
            'is_new' => 1,
            'category_id' => 3
        ],
        [
            'name' => 'Cocktail Molecular Gastronomy',
            'description' => 'Cocktail sáng tạo với kỹ thuật phân tử hiện đại, tạo ra những trải nghiệm vị giác độc đáo và thú vị.',
            'price' => 180000,
            'is_seasonal' => 1,
            'category_id' => 4
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO food_items (name, description, price, category_id, is_popular, is_new, is_seasonal, is_available, created_at)
        VALUES (:name, :description, :price, :category_id, :is_popular, :is_new, :is_seasonal, 1, NOW())
    ");

    foreach ($foods as $food) {
        $stmt->execute([
            ':name' => $food['name'],
            ':description' => $food['description'],
            ':price' => $food['price'],
            ':category_id' => $food['category_id'],
            ':is_popular' => $food['is_popular'] ?? 0,
            ':is_new' => $food['is_new'] ?? 0,
            ':is_seasonal' => $food['is_seasonal'] ?? 0
        ]);
        echo "Added: {$food['name']}\n";
    }

    echo "\n=== Current featured foods ===\n";
    $stmt = $db->query("
        SELECT id, name, is_popular, is_new, is_seasonal, price
        FROM food_items
        WHERE is_available = 1 AND (is_popular = 1 OR is_new = 1 OR is_seasonal = 1)
        ORDER BY is_popular DESC, is_new DESC, is_seasonal DESC
    ");
    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($foods as $food) {
        $type = [];
        if ($food['is_popular']) $type[] = 'Popular';
        if ($food['is_new']) $type[] = 'New';
        if ($food['is_seasonal']) $type[] = 'Seasonal';

        echo "ID: {$food['id']}, Name: {$food['name']}, Type: " . implode(', ', $type) . ", Price: " . number_format($food['price']) . "đ\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
