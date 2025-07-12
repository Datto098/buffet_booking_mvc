<?php
// Script để kiểm tra cấu trúc database
require_once __DIR__ . '/../../config/database.php';

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Kiểm tra cấu trúc database ===\n\n";

    // Kiểm tra bảng buffet_pricing
    echo "Cấu trúc bảng buffet_pricing:\n";
    try {
        $stmt = $conn->query("DESCRIBE buffet_pricing");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {$row['Field']}: {$row['Type']}\n";
        }
    } catch (Exception $e) {
        echo "  Lỗi: " . $e->getMessage() . "\n";
    }

    echo "\n";

    // Kiểm tra bảng additional_charge_types
    echo "Cấu trúc bảng additional_charge_types:\n";
    try {
        $stmt = $conn->query("DESCRIBE additional_charge_types");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {$row['Field']}: {$row['Type']}\n";
        }
    } catch (Exception $e) {
        echo "  Lỗi: " . $e->getMessage() . "\n";
    }

    echo "\n";

    // Kiểm tra bảng invoices
    echo "Cấu trúc bảng invoices:\n";
    try {
        $stmt = $conn->query("DESCRIBE invoices");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {$row['Field']}: {$row['Type']}\n";
        }
    } catch (Exception $e) {
        echo "  Lỗi: " . $e->getMessage() . "\n";
    }

} catch (PDOException $e) {
    echo "Lỗi kết nối: " . $e->getMessage() . "\n";
}
?>
