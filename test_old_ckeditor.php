<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CKEditor 4.14.1 - Old Version</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-container {
            border: 1px solid #ddd;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .info { background-color: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <h1>Test CKEditor 4.14.1 - Phiên Bản Cũ</h1>

    <div id="loading-status" class="status info">
        Đang tải CKEditor 4.14.1...
    </div>

    <div class="test-container">
        <h3>Test Form với CKEditor</h3>
        <form>
            <div style="margin-bottom: 15px;">
                <label for="title">Tiêu đề:</label><br>
                <input type="text" id="title" name="title" style="width: 100%; padding: 8px;"
                       placeholder="Nhập tiêu đề bài viết...">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="content">Nội dung:</label><br>
                <textarea id="content" name="content" rows="10" style="width: 100%;">
                    <p>Đây là nội dung mẫu để test CKEditor.</p>
                    <p><strong>Văn bản in đậm</strong> và <em>văn bản in nghiêng</em></p>
                    <ul>
                        <li>Danh sách mục 1</li>
                        <li>Danh sách mục 2</li>
                    </ul>
                </textarea>
            </div>

            <button type="button" onclick="testContent()">Test Lấy Nội dung</button>
        </form>
    </div>

    <div id="test-result" class="test-container" style="display: none;">
        <h3>Kết quả test:</h3>
        <div id="result-content"></div>
    </div>

    <!-- CKEditor 4.14.1 - Old Stable Version -->
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

    <script>
        console.log('Starting CKEditor 4.14.1 test...');

        // Check if CKEditor loaded
        function checkCKEditorStatus() {
            const statusDiv = document.getElementById('loading-status');

            if (typeof CKEDITOR !== 'undefined') {
                statusDiv.className = 'status success';
                statusDiv.innerHTML = '✅ CKEditor 4.14.1 đã tải thành công!<br>Phiên bản: ' + CKEDITOR.version;
                console.log('CKEditor version:', CKEDITOR.version);
                return true;
            } else {
                statusDiv.className = 'status error';
                statusDiv.innerHTML = '❌ CKEditor không tải được. Vui lòng kiểm tra kết nối internet.';
                console.error('CKEditor not loaded');
                return false;
            }
        }

        // Initialize CKEditor with very simple config
        function initializeCKEditor() {
            if (checkCKEditorStatus()) {
                console.log('Initializing CKEditor with simple config...');

                CKEDITOR.replace('content', {
                    height: 300,
                    language: 'vi',
                    toolbar: 'Basic'
                });

                console.log('CKEditor initialized successfully');
            }
        }

        // Test function to get content
        function testContent() {
            try {
                const editor = CKEDITOR.instances.content;
                if (editor) {
                    const content = editor.getData();
                    const resultDiv = document.getElementById('test-result');
                    const contentDiv = document.getElementById('result-content');

                    contentDiv.innerHTML = `
                        <h4>HTML Content:</h4>
                        <pre>${escapeHtml(content)}</pre>

                        <h4>Rendered Output:</h4>
                        <div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
                            ${content}
                        </div>
                    `;

                    resultDiv.style.display = 'block';

                    console.log('Content retrieved:', content);
                } else {
                    alert('CKEditor instance không tìm thấy!');
                }
            } catch (error) {
                console.error('Error getting content:', error);
                alert('Lỗi khi lấy nội dung: ' + error.message);
            }
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Wait for page to load then initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, checking CKEditor...');
            setTimeout(initializeCKEditor, 1000); // Wait 1 second for CDN to load
        });
    </script>
</body>
</html>
