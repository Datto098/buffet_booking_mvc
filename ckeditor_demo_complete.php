<?php
session_start();

// Set up constants
define('SITE_URL', 'http://localhost/buffet_booking_mvc');

// Create admin session if not exists
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_username'] = 'admin';
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$title = "Demo CKEditor Integration - Buffet News Admin";
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

    <!-- CKEditor 4 CDN - Stable Version -->
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

    <style>
        body { background-color: #f8f9fa; }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .status-indicator {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            margin: 0.5rem 0;
            font-weight: 500;
        }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-error { background-color: #f8d7da; color: #721c24; }
        .status-info { background-color: #d1ecf1; color: #0c5460; }
        .news-preview {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            background-color: white;
            min-height: 300px;
        }
        .news-preview h1, .news-preview h2, .news-preview h3 {
            color: #495057;
        }
        .toolbar-info {
            background-color: #e9ecef;
            padding: 0.75rem;
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
                        <i class="fas fa-newspaper me-2"></i>
                        Demo CKEditor Integration - Buffet News Admin
                    </h1>
                    <small class="opacity-75">Rich Text Editor cho Admin Panel</small>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-light text-dark">CKEditor 4.14.1</span>
                    <span class="badge bg-success">Admin Mode</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Status Check -->
        <div class="row mb-4">
            <div class="col-12">
                <div id="ckeditor-status" class="status-indicator status-info">
                    <i class="fas fa-clock me-1"></i>
                    Đang kiểm tra CKEditor...
                </div>
            </div>
        </div>

        <div class="row">
            <!-- News Create/Edit Form -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            Tạo/Chỉnh Sửa Tin Tức
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Toolbar Info -->
                        <div class="toolbar-info">
                            <strong>🛠️ Thanh công cụ CKEditor bao gồm:</strong>
                            <ul class="mb-0 mt-2">
                                <li>✏️ <strong>Format:</strong> Bold, Italic, Underline, Strike</li>
                                <li>📝 <strong>Lists:</strong> Numbered, Bulleted, Indent/Outdent</li>
                                <li>↔️ <strong>Align:</strong> Left, Center, Right, Justify</li>
                                <li>🔗 <strong>Links:</strong> Insert/Remove Links</li>
                                <li>📊 <strong>Insert:</strong> Table, Horizontal Rule, Special Characters</li>
                                <li>🎨 <strong>Colors:</strong> Text Color, Background Color</li>
                                <li>🔧 <strong>Tools:</strong> Styles, Format, Font, Font Size, Maximize, Source</li>
                            </ul>
                        </div>

                        <form id="news-form">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    Tiêu Đề <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="title" name="title" required
                                       value="🍤 Buffet Hải Sản Cao Cấp - Khai Trương Chi Nhánh Mới">
                            </div>

                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Tóm Tắt</label>
                                <textarea class="form-control" id="excerpt" name="excerpt" rows="2">Nhà hàng buffet hải sản cao cấp khai trương chi nhánh mới với không gian sang trọng và thực đơn đa dạng hơn 150 món ăn.</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">
                                    Nội Dung <span class="text-danger">*</span>
                                    <small class="text-muted">(Sử dụng Rich Text Editor)</small>
                                </label>
                                <textarea class="form-control" id="content" name="content" rows="12" required><h1>🎉 Chào Mừng Đến Với Buffet Hải Sản Cao Cấp</h1>

<p>Chúng tôi hân hạnh thông báo về việc <strong>khai trương chi nhánh mới</strong> của nhà hàng buffet hải sản cao cấp với không gian sang trọng và hiện đại nhất.</p>

<h2>🦞 Thực Đơn Đa Dạng</h2>

<h3>Hải sản tươi sống</h3>
<ul>
    <li><strong style="color: #d63384;">Tôm hùm Canada</strong> - Tươi sống, nướng và hấp</li>
    <li><strong style="color: #d63384;">Cua hoàng đế Alaska</strong> - Chấm với bơ tỏi</li>
    <li><strong style="color: #d63384;">Bào ngư Nam Phi</strong> - Nướng mỡ hành</li>
    <li><strong style="color: #d63384;">Nghêu sò điệp</strong> - Nướng phô mai</li>
</ul>

<h3>Món nướng BBQ cao cấp</h3>
<ul>
    <li>🥩 <em>Thịt bò Wagyu Nhật Bản</em></li>
    <li>🐷 <em>Thịt heo Iberico Tây Ban Nha</em></li>
    <li>🐑 <em>Thịt cừu New Zealand</em></li>
    <li>🐔 <em>Gà tươi nướng than hoa</em></li>
</ul>

<h2>🍣 Góc Ẩm Thực Quốc Tế</h2>

<table border="1" style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background-color: #f8f9fa;">
            <th style="padding: 12px; text-align: left;">Khu vực</th>
            <th style="padding: 12px; text-align: left;">Món đặc trưng</th>
            <th style="padding: 12px; text-align: left;">Đầu bếp</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 12px;"><strong>🇯🇵 Nhật Bản</strong></td>
            <td style="padding: 12px;">Sushi, Sashimi, Tempura</td>
            <td style="padding: 12px;">Chef Tanaka</td>
        </tr>
        <tr style="background-color: #f8f9fa;">
            <td style="padding: 12px;"><strong>🇮🇹 Ý</strong></td>
            <td style="padding: 12px;">Pizza, Pasta, Gelato</td>
            <td style="padding: 12px;">Chef Marco</td>
        </tr>
        <tr>
            <td style="padding: 12px;"><strong>🇫🇷 Pháp</strong></td>
            <td style="padding: 12px;">Bánh ngọt, Pate, Rượu vang</td>
            <td style="padding: 12px;">Chef Pierre</td>
        </tr>
    </tbody>
</table>

<h2>💰 Bảng Giá Ưu Đãi</h2>

<div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;">
    <h4 style="color: #664d03; margin-top: 0;">🎁 KHUYẾN MÃI KHAI TRƯƠNG</h4>
    <p style="color: #664d03; margin-bottom: 0;"><strong>Giảm 30% cho 100 khách hàng đầu tiên!</strong></p>
</div>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #198754; color: white;">
            <th style="padding: 12px;">Thời gian</th>
            <th style="padding: 12px;">Giá gốc</th>
            <th style="padding: 12px;">Giá khuyến mãi</th>
            <th style="padding: 12px;">Tiết kiệm</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 12px;"><strong>Trưa (11:00-14:30)</strong></td>
            <td style="padding: 12px; text-decoration: line-through;">699,000 VNĐ</td>
            <td style="padding: 12px; color: #dc3545; font-weight: bold;">489,300 VNĐ</td>
            <td style="padding: 12px; color: #198754;">209,700 VNĐ</td>
        </tr>
        <tr style="background-color: #f8f9fa;">
            <td style="padding: 12px;"><strong>Tối (17:30-22:00)</strong></td>
            <td style="padding: 12px; text-decoration: line-through;">999,000 VNĐ</td>
            <td style="padding: 12px; color: #dc3545; font-weight: bold;">699,300 VNĐ</td>
            <td style="padding: 12px; color: #198754;">299,700 VNĐ</td>
        </tr>
    </tbody>
</table>

<hr />

<h2>📍 Thông Tin Liên Hệ</h2>

<div style="background-color: #e7f3ff; padding: 20px; border-radius: 8px; text-align: center;">
    <h3 style="color: #0066cc; margin-top: 0;">📞 Hotline Đặt Bàn</h3>
    <p style="font-size: 24px; font-weight: bold; color: #dc3545; margin: 10px 0;">1900 8888</p>
    <p style="color: #666; margin-bottom: 0;"><em>Hoạt động 24/7 - Tư vấn miễn phí</em></p>
</div>

<p style="text-align: center; font-style: italic; margin-top: 30px;">
    <strong>🌟 Hãy đến và trải nghiệm hương vị tuyệt vời ngay hôm nay! 🌟</strong>
</p></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i> Reset Form
                                </button>
                                <button type="button" class="btn btn-primary" onclick="previewContent()">
                                    <i class="fas fa-eye me-1"></i> Xem Trước
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Preview Panel -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Xem Trước Nội Dung
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="content-preview" class="news-preview">
                            <p class="text-muted text-center">
                                <i class="fas fa-arrow-left me-2"></i>
                                Nhấn "Xem Trước" để hiển thị nội dung
                            </p>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#html-source-collapse">
                                <i class="fas fa-code me-1"></i> Xem HTML Source
                            </button>

                            <div class="collapse mt-2" id="html-source-collapse">
                                <textarea id="html-source" class="form-control" rows="8" readonly
                                          style="font-family: 'Courier New', monospace; font-size: 12px; background-color: #f8f9fa;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-link me-2"></i>
                            Liên Kết Nhanh
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="http://localhost/buffet_booking_mvc/admin/news/create" class="btn btn-success w-100 mb-2">
                                    <i class="fas fa-plus me-1"></i> Tạo Tin Tức Thực Tế
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="http://localhost/buffet_booking_mvc/admin/news" class="btn btn-info w-100 mb-2">
                                    <i class="fas fa-list me-1"></i> Danh Sách Tin Tức
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="http://localhost/buffet_booking_mvc/test_ckeditor_integration.php" class="btn btn-warning w-100 mb-2">
                                    <i class="fas fa-flask me-1"></i> Test CKEditor
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="http://localhost/buffet_booking_mvc" class="btn btn-secondary w-100 mb-2">
                                    <i class="fas fa-home me-1"></i> Trang Chủ Website
                                </a>
                            </div>
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
        window.SITE_URL = 'http://localhost/buffet_booking_mvc';

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Demo page loaded, initializing CKEditor...');

            // Check CKEditor status
            const statusDiv = document.getElementById('ckeditor-status');

            if (typeof CKEDITOR !== 'undefined') {
                statusDiv.className = 'status-indicator status-success';
                statusDiv.innerHTML = `<i class="fas fa-check-circle me-1"></i>CKEditor ${CKEDITOR.version} đã tải thành công!`;

                // Initialize CKEditor with full config
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
                                console.log('CKEditor ready for demo!');

                                // Auto-preview content after 2 seconds
                                setTimeout(() => {
                                    previewContent();
                                }, 2000);
                            }
                        }
                    });
                }, 500);

            } else {
                statusDiv.className = 'status-indicator status-error';
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
                    previewDiv.innerHTML = `<h2 style="color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">${title}</h2>${content}`;

                    // Show HTML source
                    document.getElementById('html-source').value = content;

                    console.log('Content previewed successfully');
                } else {
                    console.error('CKEditor instance not found');
                    alert('CKEditor chưa sẵn sàng!');
                }
            } catch (error) {
                console.error('Error previewing content:', error);
                alert('Lỗi khi xem trước: ' + error.message);
            }
        }

        // Reset form function
        function resetForm() {
            if (confirm('Bạn có chắc muốn reset form?')) {
                const editor = CKEDITOR.instances.content;
                if (editor) {
                    editor.setData('<p>Nhập nội dung mới ở đây...</p>');
                }
                document.getElementById('title').value = '';
                document.getElementById('excerpt').value = '';
                document.getElementById('content-preview').innerHTML = '<p class="text-muted text-center"><i class="fas fa-arrow-left me-2"></i>Nhấn "Xem Trước" để hiển thị nội dung</p>';
                document.getElementById('html-source').value = '';
            }
        }
    </script>
</body>
</html>
