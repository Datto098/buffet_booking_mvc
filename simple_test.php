<?php
echo "Starting test...\n";
require_once 'config/config.php';
echo "Config loaded...\n";
require_once 'models/News.php';
echo "News model loaded...\n";
$news = new News();
echo "News object created...\n";
echo "Test completed successfully!\n";
?>
