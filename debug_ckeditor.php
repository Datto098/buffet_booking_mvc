<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CKEditor Loading</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-box {
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            border: 2px solid #ddd;
        }
        .success { border-color: #28a745; background-color: #d4edda; }
        .error { border-color: #dc3545; background-color: #f8d7da; }
        .loading { border-color: #ffc107; background-color: #fff3cd; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>🔧 CKEditor Troubleshooting</h1>
        <p>Đang kiểm tra tình trạng tải CKEditor...</p>

        <div id="loading-status" class="status-box loading">
            <h4>⏳ Đang kiểm tra...</h4>
            <p>Vui lòng đợi trong giây lát...</p>
        </div>

        <div id="test-results" style="display: none;">
            <!-- Results will be shown here -->
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4>🧪 Test CKEditor</h4>
            </div>
            <div class="card-body">
                <label for="editor" class="form-label">Textarea sẽ được thay thế bằng CKEditor:</label>
                <textarea id="editor" name="editor" rows="10" cols="80">
                    <h2>Test Content</h2>
                    <p>Nếu bạn thấy một thanh công cụ phong phú ở trên, CKEditor đã được tải thành công!</p>
                    <ul>
                        <li>Bold text</li>
                        <li>Italic text</li>
                        <li>Lists và links</li>
                    </ul>
                </textarea>
            </div>
        </div>

        <div class="mt-3">
            <button onclick="checkEditorData()" class="btn btn-primary">Kiểm tra dữ liệu Editor</button>
            <button onclick="location.reload()" class="btn btn-secondary">Tải lại trang</button>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4>🛠️ Các phương pháp khắc phục</h4>
            </div>
            <div class="card-body">
                <h5>Nếu CKEditor không tải:</h5>
                <ol>
                    <li><strong>Kiểm tra kết nối internet</strong> - Mở tab mới và thử truy cập: <a href="https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js" target="_blank">CKEditor CDN</a></li>
                    <li><strong>Xóa cache trình duyệt</strong> - Nhấn Ctrl+F5 để hard refresh</li>
                    <li><strong>Thử CDN khác</strong> - Chúng ta có thể chuyển sang CDN khác hoặc tải về local</li>
                    <li><strong>Kiểm tra firewall/antivirus</strong> - Có thể đang chặn tải script từ CDN</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Try multiple CDN sources -->
    <script>
        console.log('🔍 Bắt đầu kiểm tra CKEditor...');

        // Test 1: Try primary CDN
        const script1 = document.createElement('script');
        script1.src = 'https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js';
        script1.onload = function() {
            console.log('✅ CKEditor từ CDN chính đã tải thành công');
            initializeCKEditor();
        };
        script1.onerror = function() {
            console.log('❌ CDN chính thất bại, thử CDN dự phong...');
            tryBackupCDN();
        };
        document.head.appendChild(script1);

        function tryBackupCDN() {
            // Test 2: Try alternative CDN
            const script2 = document.createElement('script');
            script2.src = 'https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.25.1/ckeditor.js';
            script2.onload = function() {
                console.log('✅ CKEditor từ CDN dự phong đã tải thành công');
                initializeCKEditor();
            };
            script2.onerror = function() {
                console.log('❌ Tất cả CDN đều thất bại');
                showError();
            };
            document.head.appendChild(script2);
        }

        function initializeCKEditor() {
            document.getElementById('loading-status').style.display = 'none';
            document.getElementById('test-results').style.display = 'block';
            document.getElementById('test-results').innerHTML = `
                <div class="status-box success">
                    <h4>✅ CKEditor đã tải thành công!</h4>
                    <p>Phiên bản: ${CKEDITOR.version}</p>
                    <p>Đang khởi tạo editor...</p>
                </div>
            `;

            try {
                CKEDITOR.replace('editor', {
                    height: 300,
                    toolbar: [
                        { name: 'document', items: ['Source'] },
                        { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                        '/',
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
                        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
                        { name: 'links', items: ['Link', 'Unlink'] },
                        { name: 'insert', items: ['Table', 'HorizontalRule'] },
                        '/',
                        { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                        { name: 'colors', items: ['TextColor', 'BGColor'] }
                    ],
                    language: 'vi',
                    resize_enabled: true,
                    removePlugins: 'elementspath',
                    on: {
                        'instanceReady': function(evt) {
                            console.log('🎉 CKEditor đã sẵn sàng sử dụng!');
                            document.getElementById('test-results').innerHTML = `
                                <div class="status-box success">
                                    <h4>🎉 CKEditor hoạt động hoàn hảo!</h4>
                                    <p>Phiên bản: ${CKEDITOR.version}</p>
                                    <p>Editor đã được khởi tạo và sẵn sàng sử dụng.</p>
                                    <p><strong>✅ Giải pháp đã hoạt động - bạn có thể sử dụng rich text editor trong admin panel!</strong></p>
                                </div>
                            `;
                        }
                    }
                });
            } catch (error) {
                console.error('❌ Lỗi khởi tạo CKEditor:', error);
                showError('Lỗi khởi tạo: ' + error.message);
            }
        }

        function showError(message = '') {
            document.getElementById('loading-status').style.display = 'none';
            document.getElementById('test-results').style.display = 'block';
            document.getElementById('test-results').innerHTML = `
                <div class="status-box error">
                    <h4>❌ CKEditor không thể tải</h4>
                    <p><strong>Lỗi:</strong> ${message || 'Không thể tải CKEditor từ các CDN'}</p>
                    <p><strong>Giải pháp:</strong></p>
                    <ul>
                        <li>Kiểm tra kết nối internet</li>
                        <li>Thử tải lại trang (Ctrl+F5)</li>
                        <li>Kiểm tra firewall/antivirus</li>
                        <li>Liên hệ admin để cài đặt CKEditor local</li>
                    </ul>
                </div>
            `;
        }

        function checkEditorData() {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor) {
                const data = CKEDITOR.instances.editor.getData();
                alert('Dữ liệu từ editor:\n\n' + data);
            } else {
                alert('CKEditor chưa được khởi tạo!');
            }
        }

        // Timeout fallback
        setTimeout(function() {
            if (typeof CKEDITOR === 'undefined') {
                console.log('⏰ Timeout - CKEditor không tải trong 10 giây');
                showError('Timeout - CDN có thể bị chặn hoặc chậm');
            }
        }, 10000);
    </script>
</body>
</html>
