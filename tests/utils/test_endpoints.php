<?php
/**
 * Test specific order management endpoints
 */

require_once 'config/config.php';

// Start session and simulate admin login
session_start();
$_SESSION['user'] = [
    'id' => 4,
    'email' => 'admin@buffetbooking.com',
    'role' => 'super_admin',
    'first_name' => 'Admin',
    'last_name' => 'User'
];

echo "<h1>Testing Order Management Endpoints</h1>\n";

// Test order details endpoint
echo "<h2>Testing Order Details Modal</h2>\n";
echo '<div id="order-details-1"></div>';
echo '<script>
// Test order details modal
fetch("/admin/orders/details/1")
    .then(response => response.text())
    .then(data => {
        document.getElementById("order-details-1").innerHTML = data;
        console.log("Order details loaded successfully");
    })
    .catch(error => {
        console.error("Error loading order details:", error);
        document.getElementById("order-details-1").innerHTML = "<p>Error loading order details</p>";
    });
</script>';

// Test print order
echo "<h2>Testing Print Order</h2>\n";
echo '<p><a href="/admin/orders/print/1" target="_blank">Open Print View for Order #1</a></p>';

// Test CSV export
echo "<h2>Testing CSV Export</h2>\n";
echo '<p><a href="/admin/orders/export-csv" target="_blank">Download Orders CSV</a></p>';

echo '<script>
function testCSVExport() {
    fetch("/admin/orders/export-csv")
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error("CSV export failed");
        })
        .then(data => {
            console.log("CSV Export successful, length:", data.length);
            // Create download link
            const blob = new Blob([data], { type: "text/csv" });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "orders_export.csv";
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error("CSV export error:", error);
        });
}
</script>';

echo '<p><button onclick="testCSVExport()">Test CSV Export Download</button></p>';

// Test filtered orders
echo "<h2>Testing Filtered Orders</h2>\n";
echo '<div id="filtered-orders"></div>';
echo '<script>
// Test filtered orders
const params = new URLSearchParams({
    status: "completed",
    limit: 5,
    offset: 0
});

fetch("/admin/orders/filtered?" + params)
    .then(response => response.json())
    .then(data => {
        let html = "<h3>Filtered Orders (Completed):</h3><ul>";
        if (data.orders && data.orders.length > 0) {
            data.orders.forEach(order => {
                html += `<li>Order #${order.order_number} - ${order.customer_name} - $${order.total_amount}</li>`;
            });
        } else {
            html += "<li>No completed orders found</li>";
        }
        html += "</ul>";
        html += `<p>Total count: ${data.total || 0}</p>`;
        document.getElementById("filtered-orders").innerHTML = html;
    })
    .catch(error => {
        console.error("Error loading filtered orders:", error);
        document.getElementById("filtered-orders").innerHTML = "<p>Error loading filtered orders</p>";
    });
</script>';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Management Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        .test-section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; }
        button { padding: 10px 15px; background: #007cba; color: white; border: none; cursor: pointer; }
        button:hover { background: #005a87; }
        a { color: #007cba; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <!-- Content already echoed above -->
</body>
</html>
