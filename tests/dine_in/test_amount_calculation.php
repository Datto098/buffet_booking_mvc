<?php
require_once '../config/config.php';
require_once '../models/Food.php';
require_once '../models/Order.php';

// Test tính toán amount
echo "=== Test tính toán amount ===\n";

$foodModel = new Food();
$orderModel = new Order();

// Lấy một số món ăn để test
$foods = $foodModel->getAvailableFoods(5);

echo "Danh sách món ăn để test:\n";
foreach ($foods as $food) {
    echo "- {$food['name']}: " . number_format($food['price'], 0, ',', '.') . " ₫\n";
}

// Test tính tổng tiền
echo "\n=== Test tính tổng tiền ===\n";
$cart_items = [
    ['food_id' => $foods[0]['id'], 'quantity' => 2],
    ['food_id' => $foods[1]['id'], 'quantity' => 1],
    ['food_id' => $foods[2]['id'], 'quantity' => 3]
];

$total_amount = 0;
foreach ($cart_items as $item) {
    $food = $foodModel->getFoodById($item['food_id']);
    $item_total = $food['price'] * $item['quantity'];
    $total_amount += $item_total;

    echo "- {$food['name']} x {$item['quantity']}: " . number_format($food['price'], 0, ',', '.') . " ₫ x {$item['quantity']} = " . number_format($item_total, 0, ',', '.') . " ₫\n";
}

echo "Tổng cộng: " . number_format($total_amount, 0, ',', '.') . " ₫\n";

// Test format currency
echo "\n=== Test format currency ===\n";
$test_amounts = [1000, 15000, 250000, 1500000, 50000000];

foreach ($test_amounts as $amount) {
    $formatted = number_format($amount, 0, ',', '.');
    echo "- {$amount} -> {$formatted} ₫\n";
}

echo "\nTest hoàn thành!\n";
?>
