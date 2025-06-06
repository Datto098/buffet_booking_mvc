<?php
echo "<h1>URL Rewriting Test</h1>";

echo "<h2>Current Request Information:</h2>";
echo "<p>REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "</p>";
echo "<p>SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "</p>";
echo "<p>PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'not set') . "</p>";
echo "<p>QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'not set') . "</p>";

// Parse URL like index.php does
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $uri = substr($uri, strlen($basePath));
}

$segments = array_filter(explode('/', $uri));
$segments = array_values($segments);

echo "<h2>Parsed Segments:</h2>";
echo "<pre>";
print_r($segments);
echo "</pre>";

if (empty($segments)) {
    $segments = ['home'];
}

$page = $segments[0] ?? 'home';
$action = $segments[1] ?? 'index';
$param = $segments[2] ?? null;

echo "<h2>Routing Variables:</h2>";
echo "<p>Page: $page</p>";
echo "<p>Action: $action</p>";
echo "<p>Param: " . ($param ?: 'null') . "</p>";
?>
