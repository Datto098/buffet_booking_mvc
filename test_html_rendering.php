<?php
// Test HTML content rendering
require_once 'config/config.php';
require_once 'config/database.php';

// Test HTML content that would come from CKEditor
$test_html_content = '
<h2>Test Rich Content</h2>
<p>This is a <strong>bold text</strong> and this is <em>italic text</em>.</p>
<ul>
    <li>First bullet point</li>
    <li>Second bullet point with <a href="#link">a link</a></li>
    <li>Third bullet point</li>
</ul>
<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th>Header 1</th>
            <th>Header 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Cell 1</td>
            <td>Cell 2</td>
        </tr>
    </tbody>
</table>
<blockquote>
    <p>This is a blockquote with formatted text.</p>
</blockquote>
<p style="text-align: center; color: #d4af37;">Centered golden text</p>
';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test HTML Rendering</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Luxury styles -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/luxury-style.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">HTML Content Rendering Test</h1>

        <div class="card">
            <div class="card-header">
                <h3>Raw HTML Input (from CKEditor)</h3>
            </div>
            <div class="card-body">
                <pre><code><?= htmlspecialchars($test_html_content) ?></code></pre>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3>Rendered HTML Output (as it appears on customer page)</h3>
            </div>
            <div class="card-body news-content-luxury">
                <?= $test_html_content ?>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3>Database Simulation Test</h3>
            </div>
            <div class="card-body">
                <?php
                try {
                    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    echo '<div class="alert alert-success">✅ Database connection successful</div>';

                    // Test inserting HTML content
                    $stmt = $pdo->prepare("SELECT 1 FROM news LIMIT 1");
                    $stmt->execute();
                    echo '<div class="alert alert-info">✅ News table exists and accessible</div>';

                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">❌ Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                ?>
            </div>
        </div>

        <div class="mt-4">
            <a href="<?= SITE_URL ?>/test_ckeditor.php" class="btn btn-primary me-2">Test CKEditor</a>
            <a href="<?= SITE_URL ?>/admin/news/create" class="btn btn-success me-2">Create News (Admin)</a>
            <a href="<?= SITE_URL ?>/news" class="btn btn-info">View News (Customer)</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
