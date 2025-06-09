<?php
// Test CKEditor integration for news forms
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CKEditor - News Forms</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- CKEditor 4 CDN - Stable Version -->
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            padding: 30px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        .status-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .preview-area {
            border: 2px dashed #dee2e6;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="text-center mb-4">
            <h1 class="display-6">
                <i class="fas fa-newspaper text-primary me-2"></i>
                Test CKEditor cho News Forms
            </h1>
            <p class="lead">Kiểm tra tích hợp rich text editor trong form tạo và chỉnh sửa tin tức</p>
        </div>

        <!-- CKEditor Status -->
        <div class="test-card">
            <h3><i class="fas fa-info-circle text-info me-2"></i>Trạng Thái CKEditor</h3>
            <div id="ckeditor-status">
                <span class="status-badge status-error">
                    <i class="fas fa-hourglass-half me-1"></i>Đang kiểm tra...
                </span>
            </div>
        </div>

        <!-- Create News Form Test -->
        <div class="test-card">
            <h3><i class="fas fa-plus-circle text-success me-2"></i>Test Form Tạo Tin Tức</h3>
            <form id="create-news-form">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Tiêu Đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required
                               value="Tin tức mẫu với Rich Text Editor">
                    </div>
                    <div class="col-md-4">
                        <label for="image" class="form-label">Hình Ảnh</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label">Tóm Tắt</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3">Đây là tóm tắt mẫu cho bài viết test CKEditor.</textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">
                        Nội Dung <span class="text-danger">*</span>
                        <small class="text-muted">(Sử dụng Rich Text Editor)</small>
                    </label>
                    <textarea class="form-control" id="content" name="content" rows="12" required
                              placeholder="Nhập nội dung chi tiết của bài viết...">
<h2>Tiêu đề chính của bài viết</h2>
<p>Đây là một đoạn văn mẫu để test <strong>rich text editor</strong> trong hệ thống quản lý tin tức.</p>

<h3>Danh sách tính năng:</h3>
<ul>
    <li>Định dạng văn bản (in đậm, in nghiêng, gạch chân)</li>
    <li>Danh sách có thứ tự và không thứ tự</li>
    <li>Căn lề văn bản</li>
    <li>Thêm liên kết và bảng</li>
    <li>Màu sắc văn bản và nền</li>
    <li>Kích thước và kiểu chữ</li>
</ul>

<h3>Bảng mẫu:</h3>
<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f8f9fa;">
            <th style="padding: 8px;">Tính năng</th>
            <th style="padding: 8px;">Mô tả</th>
            <th style="padding: 8px;">Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 8px;">Rich Text Editor</td>
            <td style="padding: 8px;">CKEditor 4.14.1</td>
            <td style="padding: 8px; color: green;"><strong>✓ Hoạt động</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">Hỗ trợ tiếng Việt</td>
            <td style="padding: 8px;">Giao diện và ngôn ngữ</td>
            <td style="padding: 8px; color: green;"><strong>✓ Hoạt động</strong></td>
        </tr>
    </tbody>
</table>

<p style="text-align: center; color: #6c757d; font-style: italic;">
    Văn bản này được tạo bằng CKEditor rich text editor
</p>
                    </textarea>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Sử dụng rich text editor để định dạng văn bản, thêm liên kết, bảng, và nhiều hơn nữa.
                    </small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                               value="Test CKEditor Rich Text Editor">
                    </div>
                    <div class="col-md-6">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="2">Bài viết test tính năng rich text editor CKEditor trong hệ thống quản lý tin tức.</textarea>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" checked>
                    <label class="form-check-label" for="is_published">Xuất Bản Ngay</label>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="previewContent()">
                        <i class="fas fa-eye me-1"></i> Xem Trước Nội Dung
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Test Lưu Bài Viết
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Area -->
        <div class="test-card" id="preview-section" style="display: none;">
            <h3><i class="fas fa-eye text-warning me-2"></i>Xem Trước Nội Dung</h3>
            <div class="preview-area" id="content-preview">
                <!-- Preview content will be inserted here -->
            </div>
        </div>

        <!-- System Information -->
        <div class="test-card">
            <h3><i class="fas fa-cog text-secondary me-2"></i>Thông Tin Hệ Thống</h3>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>CKEditor Version:</strong> <span id="ckeditor-version">Checking...</span></p>
                    <p><strong>Browser:</strong> <span id="browser-info">Checking...</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Language:</strong> Vietnamese (vi)</p>
                    <p><strong>Toolbar:</strong> Full (Standard + Custom)</p>
                    <p><strong>Height:</strong> 400px</p>
                    <p><strong>Security:</strong> XSS Protection Enabled</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Initialize CKEditor and test functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Test page loaded, checking CKEditor...');

            // Check CKEditor availability
            if (typeof CKEDITOR !== 'undefined') {
                document.getElementById('ckeditor-status').innerHTML = `
                    <span class="status-badge status-success">
                        <i class="fas fa-check-circle me-1"></i>CKEditor đã load thành công
                    </span>
                `;

                // Get CKEditor version
                document.getElementById('ckeditor-version').textContent = CKEDITOR.version || '4.14.1';

                // Initialize CKEditor
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
                            console.log('CKEditor is ready for testing!');

                            // Show success notification
                            const notification = document.createElement('div');
                            notification.className = 'alert alert-success alert-dismissible fade show';
                            notification.style.position = 'fixed';
                            notification.style.top = '20px';
                            notification.style.right = '20px';
                            notification.style.zIndex = '9999';
                            notification.innerHTML = `
                                <i class="fas fa-check-circle me-2"></i>
                                CKEditor đã sẵn sàng! Bạn có thể test các tính năng rich text editor.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.body.appendChild(notification);

                            // Auto-hide after 5 seconds
                            setTimeout(() => {
                                if (notification.parentNode) {
                                    notification.remove();
                                }
                            }, 5000);
                        }
                    }
                });
            } else {
                document.getElementById('ckeditor-status').innerHTML = `
                    <span class="status-badge status-error">
                        <i class="fas fa-times-circle me-1"></i>CKEditor không load được
                    </span>
                `;
                document.getElementById('ckeditor-version').textContent = 'Không có';
            }

            // Get browser info
            document.getElementById('browser-info').textContent = navigator.userAgent.split(')')[0].split('(')[1] || 'Unknown';

            // Form submission handler
            document.getElementById('create-news-form').addEventListener('submit', function(e) {
                e.preventDefault();

                // Update CKEditor content
                if (CKEDITOR.instances.content) {
                    CKEDITOR.instances.content.updateElement();

                    const content = CKEDITOR.instances.content.getData().trim();
                    if (!content || content === '<p></p>' || content === '<p>&nbsp;</p>') {
                        alert('Vui lòng nhập nội dung bài viết!');
                        return false;
                    }
                }

                // Show success message
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-info alert-dismissible fade show';
                successAlert.style.position = 'fixed';
                successAlert.style.top = '20px';
                successAlert.style.right = '20px';
                successAlert.style.zIndex = '9999';
                successAlert.innerHTML = `
                    <i class="fas fa-info-circle me-2"></i>
                    Đây chỉ là test form! Trong thực tế, dữ liệu sẽ được lưu vào database.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successAlert);

                setTimeout(() => {
                    if (successAlert.parentNode) {
                        successAlert.remove();
                    }
                }, 5000);
            });
        });

        // Preview content function
        function previewContent() {
            if (CKEDITOR.instances.content) {
                CKEDITOR.instances.content.updateElement();
                const content = CKEDITOR.instances.content.getData();

                document.getElementById('content-preview').innerHTML = content;
                document.getElementById('preview-section').style.display = 'block';

                // Scroll to preview
                document.getElementById('preview-section').scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('CKEditor chưa được khởi tạo!');
            }
        }
    </script>
</body>
</html>
