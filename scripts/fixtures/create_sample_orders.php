<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Order.php';
require_once 'models/User.php';
require_once 'models/Food.php';

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// Initialize models
$orderModel = new Order();
$userModel = new User();
$foodModel = new Food();

echo "<h2>Creating Sample Orders for Testing</h2>\n";

// Get some existing users
$users = $userModel->findAll();
if (empty($users)) {
    echo "No users found. Creating sample users first...\n";

    // Create sample users
    $userData = [
        [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'phone' => '1234567890',
            'role' => 'customer'
        ],
        [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'phone' => '0987654321',
            'role' => 'customer'
        ]
    ];

    foreach ($userData as $user) {
        $userModel->create($user);
        echo "Created user: {$user['first_name']} {$user['last_name']}\n";
    }

    $users = $userModel->findAll();
}

// Get some menu items or create them
$menuItems = $foodModel->findAll();
if (empty($menuItems)) {
    echo "No menu items found. Creating sample menu items...\n";
      $menuData = [
        [
            'name' => 'Deluxe Buffet',
            'description' => 'All-you-can-eat buffet with premium dishes',
            'price' => 299.00,
            'category_id' => 1,
            'is_available' => 1
        ],
        [
            'name' => 'Standard Buffet',
            'description' => 'Traditional buffet experience',
            'price' => 199.00,
            'category_id' => 1,
            'is_available' => 1
        ],
        [
            'name' => 'Vegetarian Special',
            'description' => 'Vegetarian-only buffet options',
            'price' => 179.00,
            'category_id' => 1,
            'is_available' => 1
        ]
    ];
      foreach ($menuData as $item) {
        $foodModel->create($item);
        echo "Created menu item: {$item['name']}\n";
    }

    $menuItems = $foodModel->findAll();
}

// Create sample orders
$sampleOrders = [
    [
        'user_id' => $users[0]['id'],
        'order_number' => 'ORD' . time() . '001',
        'total_amount' => 598.00,
        'status' => 'confirmed'
    ],
    [
        'user_id' => $users[0]['id'],
        'order_number' => 'ORD' . time() . '002',
        'total_amount' => 199.00,
        'status' => 'pending'
    ]
];

if (count($users) > 1) {
    $sampleOrders[] = [
        'user_id' => $users[1]['id'],
        'order_number' => 'ORD' . time() . '003',
        'total_amount' => 358.00,
        'status' => 'completed'
    ];
}

$orderIds = [];
foreach ($sampleOrders as $order) {
    $orderId = $orderModel->create($order);
    if ($orderId) {
        $orderIds[] = $orderId;
        echo "Created order #$orderId for user {$order['user_id']}\n";

        // Add order items
        if (!empty($menuItems)) {
            // Add 2 random menu items to each order
            $selectedItems = array_rand($menuItems, min(2, count($menuItems)));
            if (!is_array($selectedItems)) {
                $selectedItems = [$selectedItems];
            }

            foreach ($selectedItems as $itemIndex) {
                $menuItem = $menuItems[$itemIndex];
                $quantity = rand(1, 3);
                  $orderItemData = [
                    'order_id' => $orderId,
                    'food_item_id' => $menuItem['id'],
                    'quantity' => $quantity,
                    'price' => $menuItem['price']
                ];
                  // Insert order item
                $sql = "INSERT INTO order_items (order_id, food_item_id, quantity) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$orderId, $menuItem['id'], $quantity]);

                echo "  - Added {$quantity}x {$menuItem['name']}\n";
            }
        }
    }
}

// Update created_at and updated_at for variety
if (!empty($orderIds)) {
    $dates = [
        date('Y-m-d H:i:s', strtotime('-1 week')),
        date('Y-m-d H:i:s', strtotime('-3 days')),
        date('Y-m-d H:i:s', strtotime('-1 day'))
    ];

    foreach ($orderIds as $index => $orderId) {
        if (isset($dates[$index])) {
            $sql = "UPDATE orders SET created_at = ?, updated_at = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$dates[$index], $dates[$index], $orderId]);
        }
    }
}

echo "\nSample orders created successfully!\n";
echo "Total orders created: " . count($orderIds) . "\n";

// Display orders for verification
echo "\n<h3>Orders in Database:</h3>\n";
$orders = $orderModel->findAll();
echo "<table border='1' cellpadding='5'>\n";
echo "<tr><th>ID</th><th>User ID</th><th>Total</th><th>Status</th><th>Created</th></tr>\n";

foreach ($orders as $order) {
    echo "<tr>";
    echo "<td>{$order['id']}</td>";
    echo "<td>{$order['user_id']}</td>";
    echo "<td>\${$order['total_amount']}</td>";
    echo "<td>{$order['status']}</td>";
    echo "<td>{$order['created_at']}</td>";
    echo "</tr>\n";
}
echo "</table>\n";

echo "\n<p><strong>Ready for testing!</strong> You can now run the workflow tests again.</p>\n";
?>
