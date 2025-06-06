<?php
/**
 * Test & Scripts Dashboard
 * Trang chủ để truy cập vào thư mục tests và scripts
 */

// Tiêu đề trang
$title = "Buffet Booking MVC - Test & Scripts Dashboard";

// Đường dẫn cơ bản
$baseUrl = '/buffet_booking_mvc';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
            background-color: #f9f9f9;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .card {
            flex: 1;
            min-width: 300px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 25px;
            background-color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .card-tests {
            border-top: 5px solid #3498db;
        }
        .card-scripts {
            border-top: 5px solid #e67e22;
        }
        .card h2 {
            color: #2c3e50;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
        }
        .card p {
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3498db;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #2980b9;
        }
        .button-tests {
            background-color: #3498db;
        }
        .button-tests:hover {
            background-color: #2980b9;
        }
        .button-scripts {
            background-color: #e67e22;
        }
        .button-scripts:hover {
            background-color: #d35400;
        }
        .features {
            margin-top: 20px;
        }
        .features h3 {
            color: #2c3e50;
            font-size: 1.1em;
            margin-bottom: 10px;
        }
        .features ul {
            padding-left: 20px;
        }
        .features li {
            margin-bottom: 5px;
            color: #555;
        }
        .quick-links {
            margin-top: 40px;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .quick-links h3 {
            color: #2c3e50;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
        }
        .link-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .link-item {
            padding: 10px;
            border-radius: 5px;
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }
        .link-item:hover {
            background-color: #e8f4fc;
        }
        .link-item a {
            color: #3498db;
            text-decoration: none;
            display: block;
        }
        .link-item a:hover {
            text-decoration: underline;
        }
        footer {
            margin-top: 50px;
            text-align: center;
            color: #7f8c8d;
            font-size: 0.9em;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
    </style>
</head>
<body>
    <h1><?php echo $title; ?></h1>

    <div class="container">
        <div class="card card-tests">
            <h2>Test Directory</h2>
            <p>Thư mục tests chứa các file kiểm thử để xác minh tính đúng đắn của các chức năng trong hệ thống.</p>
            <a href="<?php echo $baseUrl; ?>/tests/" class="button button-tests">Browse Tests</a>

            <div class="features">
                <h3>Test Categories:</h3>
                <ul>
                    <li>Booking System Tests</li>
                    <li>Database Tests</li>
                    <li>Menu Tests</li>
                    <li>News Tests</li>
                    <li>Orders Tests</li>
                    <li>Utility Tests</li>
                </ul>
            </div>
        </div>

        <div class="card card-scripts">
            <h2>Scripts Directory</h2>
            <p>Thư mục scripts chứa các công cụ để cài đặt, sửa lỗi và tạo dữ liệu mẫu cho hệ thống.</p>
            <a href="<?php echo $baseUrl; ?>/scripts/" class="button button-scripts">Browse Scripts</a>

            <div class="features">
                <h3>Script Categories:</h3>
                <ul>
                    <li>Database Scripts - Cài đặt và cấu hình cơ sở dữ liệu</li>
                    <li>Fixes Scripts - Sửa lỗi hệ thống</li>
                    <li>Fixtures Scripts - Tạo dữ liệu mẫu</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="quick-links">
        <h3>Liên kết nhanh</h3>
        <div class="link-grid">
            <div class="link-item">
                <a href="<?php echo $baseUrl; ?>/tests/booking/">🧪 Booking Tests</a>
            </div>
            <div class="link-item">
                <a href="<?php echo $baseUrl; ?>/tests/menu/">🧪 Menu Tests</a>
            </div>
            <div class="link-item">
                <a href="<?php echo $baseUrl; ?>/tests/orders/">🧪 Orders Tests</a>
            </div>
            <div class="link-item">
                <a href="<?php echo $baseUrl; ?>/tests/database/">🧪 Database Tests</a>
            </div>
            <div class="link-item">
                <a href="<?php echo $baseUrl; ?>/scripts/database/">⚙️ Database Scripts</a>
            </div>
            <div class="link-item">
                <a href="<?php echo $baseUrl; ?>/scripts/fixes/">⚙️ Fixes Scripts</a>
            </div>
            <div class="link-item">
                <a href="<?php echo $baseUrl; ?>/scripts/fixtures/">⚙️ Fixtures Scripts</a>
            </div>
            <div class="link-item">
                <a href="<?php echo $baseUrl; ?>/index.php">🏠 Home Page</a>
            </div>
        </div>
    </div>

    <footer>
        <p>Buffet Booking MVC - Test & Scripts Dashboard</p>
        <p><small>Designed for better project management and testing</small></p>
    </footer>
</body>
</html>
