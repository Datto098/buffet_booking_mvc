<?php
echo "<h2>Test Invoice System Complete Flow</h2>\n";

echo "<h3>Test Cases:</h3>\n";
echo "<ol>";
echo "<li><a href='http://localhost/buffet_booking_mvc/admin/invoice/view/1' target='_blank'>View Invoice 1</a> - Should work without errors</li>";
echo "<li><a href='http://localhost/buffet_booking_mvc/admin/invoice/print/1' target='_blank'>Print Invoice 1</a> - Should work without PDF errors</li>";
echo "<li><a href='http://localhost/buffet_booking_mvc/admin/dine-in-orders' target='_blank'>Dine-in Orders Page</a> - Check if 'Tạo hóa đơn' button appears for completed orders</li>";
echo "<li><a href='http://localhost/buffet_booking_mvc/admin/invoice/create?order_id=15' target='_blank'>Create Invoice for Order 15</a> - Should show create form (need admin login)</li>";
echo "</ol>";

echo "<h3>JavaScript Test - Payment Status Update:</h3>\n";
echo "<p>Visit <a href='http://localhost/buffet_booking_mvc/admin/invoice/view/1' target='_blank'>Invoice View</a> and click 'Đánh dấu đã thanh toán' button.</p>";
echo "<p>Check browser console for errors. The function should be <code>updateInvoicePaymentStatus()</code> not <code>updatePaymentStatus()</code></p>";

echo "<h3>Quick Database Check:</h3>\n";
require_once 'config/config.php';
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);

    // Check invoices table
    echo "<h4>Current Invoices:</h4>\n";
    $stmt = $pdo->query("SELECT id, order_id, invoice_number, payment_status, total_amount FROM invoices ORDER BY id DESC LIMIT 5");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Order ID</th><th>Invoice Number</th><th>Payment Status</th><th>Total</th></tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['order_id']}</td>";
        echo "<td>{$row['invoice_number']}</td>";
        echo "<td><strong>{$row['payment_status']}</strong></td>";
        echo "<td>" . number_format($row['total_amount']) . "đ</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check completed orders without invoices
    echo "<h4>Completed Orders without Invoices:</h4>\n";
    $stmt = $pdo->query("
        SELECT dio.id, dio.table_id, dio.total_amount, dio.status, t.table_number
        FROM dine_in_orders dio
        LEFT JOIN tables t ON dio.table_id = t.id
        LEFT JOIN invoices i ON dio.id = i.order_id
        WHERE dio.status = 'completed' AND i.id IS NULL
        ORDER BY dio.id DESC LIMIT 5
    ");

    echo "<table border='1'>";
    echo "<tr><th>Order ID</th><th>Table</th><th>Amount</th><th>Status</th><th>Action</th></tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['table_number']}</td>";
        echo "<td>" . number_format($row['total_amount']) . "đ</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td><a href='http://localhost/buffet_booking_mvc/admin/invoice/create?order_id={$row['id']}' target='_blank'>Create Invoice</a></td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage();
}
?>
