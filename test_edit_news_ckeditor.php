<?php
session_start();

// Set up constants
define('SITE_URL', 'http://localhost/buffet_booking_mvc');

// Database connection
try {
    $host = "localhost";
    $dbname = "buffet_booking";
    $username = "root";
    $password = "";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Create admin session if not exists
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_username'] = 'admin';
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get a sample news article for editing
$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 1");
$news_item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news_item) {
    // Create a sample news if none exists
    $sampleContent = '<h2>🍤 Buffet Hải Sản Tươi Ngon</h2>

<p>Chào mừng bạn đến với <strong>nhà hàng buffet hải sản</strong> cao cấp nhất thành phố!</p>

<h3>Thực đơn đa dạng</h3>
<ul>
    <li><strong style="color: #d63384;">Hải sản tươi sống:</strong> Tôm hùm, cua hoàng đế, bào ngư</li>
    <li><strong style="color: #d63384;">Món nướng BBQ:</strong> Thịt bò Wagyu, thịt heo iberico</li>
    <li><strong style="color: #d63384;">Món Á - Âu:</strong> Sushi, sashimi, pasta, pizza</li>
</ul>

<p style="text-align: center;"><em>Hãy đến và trải nghiệm ngay hôm nay!</em></p>';

    $insertStmt = $pdo->prepare("INSERT INTO news (title, excerpt, content, is_published, created_at) VALUES (?, ?, ?, 1, NOW())");
    $insertStmt->execute([
        'Buffet Hải Sản Cao Cấp - Test Article',
        'Nhà hàng buffet hải sản với thực đơn đa dạng và chất lượng cao.',
        $sampleContent
    ]);

    $news_item = [
        'id' => $pdo->lastInsertId(),
        'title' => 'Buffet Hải Sản Cao Cấp - Test Article',
        'excerpt' => 'Nhà hàng buffet hải sản với thực đơn đa dạng và chất lượng cao.',
        'content' => $sampleContent,
        'is_published' => 1
    ];
}

$title = "Test CKEditor - Edit News Admin";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
    <title><?php echo $title; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- CKEditor 4 CDN - Same version as admin -->
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

    <style>
        body { background-color: #f8f9fa; }
        .admin-header {
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .card-header {
            background-color: #495057;
            color: white;
            border-bottom: none;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            margin: 0.5rem 0;
            font-weight: 500;
        }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-error { background-color: #f8d7da; color: #721c24; }
        .status-info { background-color: #d1ecf1; color: #0c5460; }
        .preview-panel {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            background-color: white;
            min-height: 400px;
            max-height: 600px;
            overflow-y: auto;
        }
        .news-meta {
            background-color: #e9ecef;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Test CKEditor - Edit News Admin
                    </h1>
                    <small class="opacity-75">Kiểm tra Rich Text Editor trong form chỉnh sửa tin tức</small>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-light text-dark">CKEditor 4.14.1</span>
                    <span class="badge bg-success">Edit Mode</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Status Check -->
        <div class="row mb-4">
            <div class="col-12">
                <div id="ckeditor-status" class="status-badge status-info">
                    <i class="fas fa-clock me-1"></i>
                    Đang kiểm tra CKEditor...
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Edit Form -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Chỉnh Sửa Tin Tức
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Article Info -->
                        <div class="news-meta">
                            <h6><i class="fas fa-info-circle me-1"></i> Thông tin bài viết:</h6>
                            <ul class="mb-0">
                                <li><strong>ID:</strong> <?php echo $news_item['id']; ?></li>
                                <li><strong>Trạng thái:</strong>
                                    <?php echo $news_item['is_published'] ? '<span class="badge bg-success">Đã xuất bản</span>' : '<span class="badge bg-warning">Nháp</span>'; ?>
                                </li>
                                <li><strong>Ngày tạo:</strong> <?php echo $news_item['created_at'] ?? 'N/A'; ?></li>
                            </ul>
                        </div>

                        <form id="edit-news-form" action="#" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="news_id" value="<?php echo $news_item['id']; ?>">

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="title" class="form-label">Tiêu Đề <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" required
                                           value="<?php echo htmlspecialchars($news_item['title'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="is_published" class="form-label">Trạng thái</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1"
                                               <?php echo (!empty($news_item['is_published']) && $news_item['is_published']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_published">Xuất Bản Ngay</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Tóm Tắt</label>
                                <textarea class="form-control" id="excerpt" name="excerpt" rows="2"><?php echo htmlspecialchars($news_item['excerpt'] ?? ''); ?></textarea>
                                <small class="text-muted">Mô tả ngắn gọn về bài viết.</small>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">
                                    Nội Dung <span class="text-danger">*</span>
                                    <small class="text-muted">(Rich Text Editor)</small>
                                </label>
                                <textarea class="form-control" id="content" name="content" rows="12" required><?php echo htmlspecialchars($news_item['content'] ?? ''); ?></textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Sử dụng toolbar để định dạng văn bản, thêm liên kết, bảng biểu, v.v.
                                </small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i> Khôi phục
                                </button>
                                <div>
                                    <button type="button" class="btn btn-info me-2" onclick="previewContent()">
                                        <i class="fas fa-eye me-1"></i> Xem trước
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="saveContent()">
                                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6><i class="fas fa-link me-1"></i> Liên kết nhanh:</h6>
                        <a href="<?php echo SITE_URL; ?>/admin/news/edit/<?php echo $news_item['id']; ?>" class="btn btn-sm btn-primary me-2">
                            <i class="fas fa-edit me-1"></i> Edit thực tế
                        </a>
                        <a href="<?php echo SITE_URL; ?>/admin/news" class="btn btn-sm btn-secondary me-2">
                            <i class="fas fa-list me-1"></i> Danh sách tin tức
                        </a>
                        <a href="<?php echo SITE_URL; ?>/admin/news/create" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i> Tạo tin tức mới
                        </a>
                    </div>
                </div>
            </div>

            <!-- Preview Panel -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Xem Trước
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="content-preview" class="preview-panel">
                            <p class="text-muted text-center">
                                <i class="fas fa-arrow-left me-2"></i>
                                Nhấn "Xem trước" để hiển thị nội dung
                            </p>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#html-source-collapse">
                                <i class="fas fa-code me-1"></i> HTML Source
                            </button>

                            <div class="collapse mt-2" id="html-source-collapse">
                                <textarea id="html-source" class="form-control" rows="6" readonly
                                          style="font-family: 'Courier New', monospace; font-size: 11px; background-color: #f8f9fa;"></textarea>
                            </div>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-1"></i>
                                <strong>Tips:</strong> Sử dụng "Maximize" trong toolbar để chỉnh sửa toàn màn hình.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Set up site URL
        window.SITE_URL = '<?php echo SITE_URL; ?>';
        let originalContent = '<?php echo addslashes($news_item['content'] ?? ''); ?>';

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Edit test page loaded, initializing CKEditor...');

            // Check CKEditor status
            const statusDiv = document.getElementById('ckeditor-status');

            if (typeof CKEDITOR !== 'undefined') {
                statusDiv.className = 'status-badge status-success';
                statusDiv.innerHTML = `<i class="fas fa-check-circle me-1"></i>CKEditor ${CKEDITOR.version} sẵn sàng cho chỉnh sửa!`;

                // Initialize CKEditor
                setTimeout(() => {
                    CKEDITOR.replace('content', {
                        height: 400,
                        language: 'vi',
                        toolbar: [
                            ['Bold', 'Italic', 'Underline', 'Strike'],
                            ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
                            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                            ['Link', 'Unlink'],
                            ['Table', 'HorizontalRule', 'SpecialChar'],
                            ['TextColor', 'BGColor'],
                            ['Styles', 'Format', 'Font', 'FontSize'],
                            ['Maximize', 'Source']
                        ],
                        fontSize_sizes: '12/12px;14/14px;16/16px;18/18px;20/20px;24/24px;28/28px;32/32px;36/36px',
                        format_tags: 'p;h1;h2;h3;h4;h5;h6;pre;address;div',
                        removeButtons: 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Flash,Smiley,PageBreak,Iframe',
                        resize_enabled: true,
                        removePlugins: 'elementspath',
                        on: {
                            'instanceReady': function(evt) {
                                console.log('CKEditor ready for editing!');
                                // Auto-preview content
                                setTimeout(previewContent, 1000);
                            }
                        }
                    });
                }, 500);

            } else {
                statusDiv.className = 'status-badge status-error';
                statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>CKEditor không tải được!';
            }
        });

        // Preview content function
        function previewContent() {
            try {
                const editor = CKEDITOR.instances.content;
                if (editor) {
                    const content = editor.getData();
                    const title = document.getElementById('title').value;

                    // Show preview
                    const previewDiv = document.getElementById('content-preview');
                    previewDiv.innerHTML = `
                        <h3 style="color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 10px; margin-bottom: 15px;">
                            ${title || 'Tiêu đề bài viết'}
                        </h3>
                        ${content}
                    `;

                    // Show HTML source
                    document.getElementById('html-source').value = content;

                    console.log('Content previewed successfully');
                } else {
                    alert('CKEditor chưa sẵn sàng!');
                }
            } catch (error) {
                console.error('Error previewing content:', error);
                alert('Lỗi khi xem trước: ' + error.message);
            }
        }

        // Reset form function
        function resetForm() {
            if (confirm('Bạn có chắc muốn khôi phục nội dung gốc?')) {
                const editor = CKEDITOR.instances.content;
                if (editor) {
                    editor.setData(originalContent);
                }
                document.getElementById('title').value = '<?php echo addslashes($news_item['title'] ?? ''); ?>';
                document.getElementById('excerpt').value = '<?php echo addslashes($news_item['excerpt'] ?? ''); ?>';

                setTimeout(previewContent, 500);
            }
        }

        // Save content function (simulation)
        function saveContent() {
            try {
                const editor = CKEDITOR.instances.content;
                if (editor) {
                    const content = editor.getData();
                    const title = document.getElementById('title').value;

                    if (!title.trim()) {
                        alert('Vui lòng nhập tiêu đề!');
                        return;
                    }

                    if (!content.trim() || content === '<p></p>') {
                        alert('Vui lòng nhập nội dung!');
                        return;
                    }

                    // Simulate save (in real app, this would submit to server)
                    const saveBtn = event.target;
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang lưu...';

                    setTimeout(() => {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fas fa-save me-1"></i> Lưu thay đổi';

                        alert(`✅ Đã lưu thay đổi thành công!\n\n📝 Tiêu đề: ${title}\n📊 Độ dài nội dung: ${content.length} ký tự`);
                    }, 2000);

                    console.log('Content saved:', { title, content });
                }
            } catch (error) {
                console.error('Error saving content:', error);
                alert('Lỗi khi lưu: ' + error.message);
            }
        }
    </script>
</body>
</html>
