<?php
// Test routing simulation
require_once 'config/config.php';

echo "=== ROUTING SIMULATION TEST ===\n\n";

// Simulate different URLs
$testUrls = [
    '/buffet_booking_mvc/' => ['home', 'index', null],
    '/buffet_booking_mvc/about' => ['about', 'index', null],
    '/buffet_booking_mvc/promotions' => ['promotions', 'index', null]
];

foreach ($testUrls as $url => $expected) {
    echo "Testing URL: $url\n";
    echo "Expected: page={$expected[0]}, action={$expected[1]}, param={$expected[2]}\n";

    // Parse like index.php does
    $uri = parse_url($url, PHP_URL_PATH);
    $uri = rtrim($uri, '/');

    $basePath = '/buffet_booking_mvc';
    if ($basePath !== '/') {
        $uri = substr($uri, strlen($basePath));
    }

    $segments = array_filter(explode('/', $uri));
    $segments = array_values($segments);

    if (empty($segments)) {
        $segments = ['home'];
    }

    $page = $segments[0] ?? 'home';
    $action = $segments[1] ?? 'index';
    $param = $segments[2] ?? null;

    echo "Actual: page=$page, action=$action, param=" . ($param ?: 'null') . "\n";

    // Test routing logic
    $routes = [
        'home' => 'controllers/HomeController.php',
        'about' => 'controllers/HomeController.php',
        'promotions' => 'controllers/HomeController.php',
    ];

    if (isset($routes[$page])) {
        echo "✓ Route found: {$routes[$page]}\n";

        $controllerMap = [
            'about' => 'HomeController',
            'promotions' => 'HomeController',
        ];

        $controllerClass = isset($controllerMap[$page]) ? $controllerMap[$page] : ucfirst($page) . 'Controller';
        echo "Controller class: $controllerClass\n";

        // Test method call logic
        if ($page === 'about') {
            echo "Would call: \$controller->about()\n";
        } elseif ($page === 'promotions') {
            echo "Would call: \$controller->promotions()\n";
        } else {
            echo "Would call: \$controller->index()\n";
        }
    } else {
        echo "✗ Route not found\n";
    }

    echo "---\n\n";
}

echo "=== SIMULATION COMPLETE ===\n";
?>
