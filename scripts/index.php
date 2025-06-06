<?php
/**
 * Scripts Directory Index
 * Hiển thị danh sách các script trong thư mục và thư mục con
 */

// Tiêu đề trang
$title = "Buffet Booking MVC - Scripts Directory";

// Thư mục gốc
$baseDir = __DIR__;
$baseUrl = '/buffet_booking_mvc/scripts';

// Lấy đường dẫn hiện tại
$currentPath = isset($_GET['path']) ? $_GET['path'] : '';
$fullPath = $baseDir . '/' . $currentPath;

// Kiểm tra đường dẫn hợp lệ
if (!file_exists($fullPath) || !is_dir($fullPath)) {
    $currentPath = '';
    $fullPath = $baseDir;
}

// Lấy danh sách file và thư mục
$items = scandir($fullPath);
$directories = [];
$files = [];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;

    $itemPath = $fullPath . '/' . $item;

    if (is_dir($itemPath)) {
        $directories[] = $item;
    } else if (pathinfo($item, PATHINFO_EXTENSION) === 'php' || pathinfo($item, PATHINFO_EXTENSION) === 'md') {
        $files[] = $item;
    }
}

// Sắp xếp
sort($directories);
sort($files);

// Hiển thị giao diện
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
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }
        .breadcrumbs {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .breadcrumbs a {
            color: #3498db;
            text-decoration: none;
        }
        .breadcrumbs a:hover {
            text-decoration: underline;
        }
        .item-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }
        .item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            transition: all 0.3s ease;
        }
        .item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .directory {
            background-color: #e8f4fc;
        }
        .file {
            background-color: #f8f9fa;
        }
        .item a {
            color: #2980b9;
            text-decoration: none;
            font-weight: bold;
            display: block;
        }
        .item a:hover {
            text-decoration: underline;
        }
        .description {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .php-file {
            color: #e67e22;
        }
        .md-file {
            color: #8e44ad;
        }
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
        .warning {
            background-color: #fcf8e3;
            border-left: 5px solid #f39c12;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1><?php echo $title; ?></h1>

    <div class="breadcrumbs">
        <a href="<?php echo $baseUrl; ?>">Scripts Home</a>
        <?php
        $paths = explode('/', $currentPath);
        $breadcrumbPath = '';

        foreach ($paths as $path) {
            if (empty($path)) continue;

            $breadcrumbPath .= '/' . $path;
            echo ' &raquo; <a href="' . $baseUrl . '/index.php?path=' . ltrim($breadcrumbPath, '/') . '">' . $path . '</a>';
        }
        ?>
    </div>

    <div class="warning">
        <strong>Lưu ý:</strong> Các script trong thư mục này có thể thay đổi cấu trúc và dữ liệu trong cơ sở dữ liệu.
        Hãy cẩn thận khi chạy và nên sao lưu dữ liệu trước khi thực hiện.
    </div>

    <?php if (!empty($currentPath)): ?>
    <a href="<?php
        $parentPath = dirname($currentPath);
        if ($parentPath === '.') {
            $parentPath = '';
        }
        echo $baseUrl . '/index.php?path=' . $parentPath;
    ?>" class="back-button">&laquo; Back to Parent Directory</a>
    <?php endif; ?>

    <div class="item-list">
        <?php foreach ($directories as $dir): ?>
            <div class="item directory">
                <a href="<?php echo $baseUrl . '/index.php?path=' . ($currentPath ? $currentPath . '/' : '') . $dir; ?>">
                    📁 <?php echo $dir; ?>
                </a>
                <div class="description">Directory</div>
            </div>
        <?php endforeach; ?>

        <?php foreach ($files as $file): ?>
            <?php
            $fileExt = pathinfo($file, PATHINFO_EXTENSION);
            $fileUrl = $baseUrl . '/' . ($currentPath ? $currentPath . '/' : '') . $file;

            // Get file description if it's a PHP file
            $description = '';
            if ($fileExt === 'php') {
                $fileContent = file_get_contents($fullPath . '/' . $file);
                preg_match('/\/\*\*\s*\n\s*\*\s*(.*?)\s*\n/s', $fileContent, $matches);
                if (!empty($matches[1])) {
                    $description = trim($matches[1]);
                }
            } else if ($fileExt === 'md') {
                $fileContent = file_get_contents($fullPath . '/' . $file);
                $lines = explode("\n", $fileContent);
                if (!empty($lines[0])) {
                    $description = str_replace('#', '', trim($lines[0]));
                }
            }
            ?>
            <div class="item file">
                <a href="<?php echo $fileUrl; ?>" class="<?php echo $fileExt; ?>-file">
                    <?php echo ($fileExt === 'php') ? '⚙️' : '📄'; ?> <?php echo $file; ?>
                </a>
                <div class="description"><?php echo $description ?: ucfirst($fileExt) . ' file'; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($directories) && empty($files)): ?>
        <p>No files or directories found in this path.</p>
    <?php endif; ?>

    <footer style="margin-top: 30px; border-top: 1px solid #ecf0f1; padding-top: 10px; color: #7f8c8d; font-size: 0.9em;">
        <p>Buffet Booking MVC - Scripts Directory Browser</p>
    </footer>
</body>
</html>
