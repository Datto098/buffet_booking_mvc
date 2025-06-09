<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CKEditor Integration - Buffet News Admin</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .card-header {
            background-color: #495057;
            color: white;
            font-weight: 500;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            margin: 0.5rem 0;
            display: inline-block;
        }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-error { background-color: #f8d7da; color: #721c24; }
        .status-info { background-color: #d1ecf1; color: #0c5460; }
        .preview-content {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            background-color: white;
            min-height: 200px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="bi bi-newspaper me-2"></i>
                    Test CKEditor Integration - Buffet News Admin
                </h1>

                <div id="ckeditor-status" class="status-badge status-info">
                    <i class="bi bi-clock me-1"></i>
                    Đang kiểm tra CKEditor...
                </div>
            </div>
        </div>

        <div class="row">
            <!-- News Create Form -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle me-2"></i>
                            Form Tạo Tin Tức Mới
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="create-form">
                            <div class="mb-3">
                                <label for="create-title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="create-title" name="title"
                                       value="Tin tức mẫu - Buffet Hải Sản Cao Cấp" required>
                            </div>

                            <div class="mb-3">
                                <label for="create-excerpt" class="form-label">Tóm tắt</label>
                                <textarea class="form-control" id="create-excerpt" name="excerpt" rows="2">
Khám phá thực đơn buffet hải sản tươi ngon với hơn 100 món ăn đặc sắc.
                                </textarea>
                            </div>

                            <div class="mb-3">
                                <label for="create-content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="create-content" name="content" rows="8" required>
<h2>Buffet Hải Sản Cao Cấp</h2>

<p>Chào mừng bạn đến với <strong>nhà hàng buffet hải sản</strong> cao cấp hàng đầu thành phố!</p>

<h3>Thực đơn đa dạng</h3>
<ul>
    <li><strong>Hải sản tươi sống:</strong> Tôm hùm, cua hoàng đế, bào ngư</li>
    <li><strong>Món nướng BBQ:</strong> Thịt bò Wagyu, thịt heo iberico</li>
    <li><strong>Món Á - Âu:</strong> Sushi, sashimi, pasta, pizza</li>
    <li><strong>Tráng miệng:</strong> Bánh ngọt Pháp, kem gelato Ý</li>
</ul>

<h3>Khuyến mãi đặc biệt</h3>
<p style="color: #dc3545; font-weight: bold;">🎉 Giảm giá 20% cho khách đặt bàn trước 7 ngày!</p>

<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f8f9fa;">
            <th style="padding: 8px;">Thời gian</th>
            <th style="padding: 8px;">Giá người lớn</th>
            <th style="padding: 8px;">Giá trẻ em</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 8px;">Trưa (11:00-14:00)</td>
            <td style="padding: 8px;">499,000 VNĐ</td>
            <td style="padding: 8px;">299,000 VNĐ</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Tối (17:00-22:00)</td>
            <td style="padding: 8px;">699,000 VNĐ</td>
            <td style="padding: 8px;">399,000 VNĐ</td>
        </tr>
    </tbody>
</table>

<p><em>Hãy đặt bàn ngay hôm nay để trải nghiệm!</em></p>
                                </textarea>
                            </div>

                            <div class="d-grid">
                                <button type="button" class="btn btn-primary" onclick="testCreateForm()">
                                    <i class="bi bi-eye me-1"></i>
                                    Xem trước nội dung
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Content Preview -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-eye me-2"></i>
                            Xem trước nội dung
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="content-preview" class="preview-content">
                            <p class="text-muted text-center">
                                <i class="bi bi-arrow-left me-1"></i>
                                Nhấn "Xem trước nội dung" để hiển thị HTML
                            </p>
                        </div>

                        <div class="mt-3">
                            <h6>HTML Source:</h6>
                            <textarea id="html-source" class="form-control" rows="5" readonly style="font-family: monospace; font-size: 12px;"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Results -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            Kết quả kiểm tra
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="test-results">
                            <div class="status-badge status-info">
                                <i class="bi bi-hourglass-split me-1"></i>
                                Đang khởi tạo kiểm tra...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- CKEditor 4 CDN - Same version as admin -->
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

    <script>
        let testResults = [];

        // Test function for create form
        function testCreateForm() {
            try {
                const editor = CKEDITOR.instances['create-content'];
                if (editor) {
                    const content = editor.getData();
                    const title = document.getElementById('create-title').value;

                    // Display preview
                    document.getElementById('content-preview').innerHTML = content;
                    document.getElementById('html-source').value = content;

                    // Log test result
                    addTestResult('success', `✅ Lấy nội dung thành công - Độ dài: ${content.length} ký tự`);

                    console.log('Content retrieved successfully:', content);
                } else {
                    addTestResult('error', '❌ Không tìm thấy CKEditor instance');
                }
            } catch (error) {
                addTestResult('error', `❌ Lỗi khi lấy nội dung: ${error.message}`);
                console.error('Error getting content:', error);
            }
        }

        // Add test result to display
        function addTestResult(type, message) {
            const resultsDiv = document.getElementById('test-results');
            const statusClass = type === 'success' ? 'status-success' :
                               type === 'error' ? 'status-error' : 'status-info';

            const resultHtml = `<div class="status-badge ${statusClass}">${message}</div>`;
            resultsDiv.innerHTML += resultHtml;

            // Scroll to bottom
            resultsDiv.scrollTop = resultsDiv.scrollHeight;
        }

        // Initialize CKEditor when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Testing CKEditor integration...');

            // Check CKEditor availability
            const statusDiv = document.getElementById('ckeditor-status');

            if (typeof CKEDITOR !== 'undefined') {
                statusDiv.className = 'status-badge status-success';
                statusDiv.innerHTML = `<i class="bi bi-check-circle me-1"></i>CKEditor ${CKEDITOR.version} đã tải thành công!`;

                addTestResult('success', `✅ CKEditor ${CKEDITOR.version} khả dụng`);

                // Initialize CKEditor for create form
                setTimeout(() => {
                    CKEDITOR.replace('create-content', {
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
                                addTestResult('success', '✅ CKEditor khởi tạo thành công cho form tạo tin tức');
                                console.log('CKEditor ready for create form');

                                // Auto-test after 2 seconds
                                setTimeout(() => {
                                    testCreateForm();
                                }, 2000);
                            },
                            'change': function(evt) {
                                console.log('Content changed in editor');
                            }
                        }
                    });
                }, 500);

            } else {
                statusDiv.className = 'status-badge status-error';
                statusDiv.innerHTML = '<i class="bi bi-x-circle me-1"></i>CKEditor không tải được!';

                addTestResult('error', '❌ CKEditor không khả dụng - Kiểm tra kết nối internet');
            }
        });
    </script>
</body>
</html>
