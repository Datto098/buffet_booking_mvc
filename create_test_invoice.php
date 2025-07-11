<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

// Tạo test invoice nếu chưa có
try {
    // Kiểm tra có invoice nào không
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM invoices");
    $stmt->execute();
    $count = $stmt->fetchColumn();

    echo "Số hóa đơn hiện có: " . $count . "<br>";

    if ($count == 0) {
        echo "Tạo hóa đơn test...<br>";

        // Tạo test dine_in_order trước
        $stmt = $pdo->prepare("INSERT INTO dine_in_orders (table_id, user_id, status, total_amount, created_at) VALUES (1, 1, 'completed', 100000, NOW())");
        $stmt->execute();
        $orderId = $pdo->lastInsertId();
        echo "Tạo order test: ID " . $orderId . "<br>";

        // Tạo test invoice
        $stmt = $pdo->prepare("
            INSERT INTO invoices (
                order_id, invoice_number, adult_count, child_count,
                adult_price, child_price, buffet_total, food_total,
                additional_charges, additional_total, subtotal,
                tax_rate, tax_amount, total_amount, payment_method,
                payment_status, notes, created_by, created_at
            ) VALUES (
                ?, 'INV20250711001', 2, 1,
                299000, 199000, 697000, 100000,
                '[]', 0, 797000,
                0.00, 0.00, 797000, 'cash',
                'pending', 'Test invoice', 1, NOW()
            )
        ");
        $stmt->execute([$orderId]);
        $invoiceId = $pdo->lastInsertId();
        echo "Tạo invoice test: ID " . $invoiceId . "<br>";

        echo "<a href='admin/invoice/view/" . $invoiceId . "'>Xem hóa đơn test</a>";
    } else {
        // Lấy invoice đầu tiên
        $stmt = $pdo->prepare("SELECT id FROM invoices ORDER BY id LIMIT 1");
        $stmt->execute();
        $invoiceId = $stmt->fetchColumn();
        echo "<a href='admin/invoice/view/" . $invoiceId . "'>Xem hóa đơn ID " . $invoiceId . "</a>";
    }

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
