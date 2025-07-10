<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Cấu trúc bảng users:\n";
    $result = $db->query('DESCRIBE users');
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }

    echo "\nDữ liệu mẫu trong bảng users:\n";
    $users = $db->query('SELECT id, name, email, role FROM users LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
    foreach($users as $user) {
        echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}\n";
    }

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>
