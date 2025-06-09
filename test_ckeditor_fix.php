<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 CKEditor Fix Verification</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Fallback Editor CSS -->
    <link rel="stylesheet" href="assets/css/fallback-editor.css">
    <style>
        .test-section {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .test-success { border-color: #28a745; background-color: #f8fff9; }
        .test-warning { border-color: #ffc107; background-color: #fffdf5; }
        .test-error { border-color: #dc3545; background-color: #fff8f8; }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-success { background-color: #28a745; }
        .status-warning { background-color: #ffc107; }
        .status-error { background-color: #dc3545; }
        .status-loading { background-color: #6c757d; animation: pulse 1s infinite; }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="text-center mb-4">
            <h1><i class="fas fa-tools text-primary me-2"></i>CKEditor Fix Verification</h1>
            <p class="lead">Kiểm tra và khắc phục sự cố CKEditor không tải</p>
        </div>

        <!-- Status Overview -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div id="ckeditor-status" class="status-indicator status-loading"></div>
                        <h5>CKEditor Status</h5>
                        <span id="ckeditor-status-text">Đang kiểm tra...</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div id="fallback-status" class="status-indicator status-loading"></div>
                        <h5>Fallback Editor</h5>
                        <span id="fallback-status-text">Đang kiểm tra...</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div id="overall-status" class="status-indicator status-loading"></div>
                        <h5>Overall Status</h5>
                        <span id="overall-status-text">Đang kiểm tra...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Editor -->
        <div class="test-section" id="editor-test">
            <h3><i class="fas fa-edit me-2"></i>Test Rich Text Editor</h3>
            <p>Thử nghiệm trình soạn thảo rich text với nội dung mẫu:</p>

            <form>
                <div class="mb-3">
                    <label for="test-editor" class="form-label">Nội dung bài viết:</label>
                    <textarea id="test-editor" name="content" rows="10" class="form-control" data-rich-editor="true">
                        <h2>🎉 Chào mừng đến với trình soạn thảo!</h2>
                        <p>Đây là một đoạn văn bản mẫu với <strong>chữ đậm</strong> và <em>chữ nghiêng</em>.</p>
                        <ul>
                            <li>Danh sách có dấu đầu dòng</li>
                            <li>Hỗ trợ <a href="#link">liên kết</a></li>
                            <li>Và nhiều tính năng khác</li>
                        </ul>
                        <p style="text-align: center; color: #d4af37;">✨ Văn bản căn giữa với màu vàng ✨</p>
                        <blockquote>
                            <p>Đây là một trích dẫn để test blockquote</p>
                        </blockquote>
                    </textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="getEditorContent()">
                        <i class="fas fa-eye me-1"></i>Xem nội dung
                    </button>
                    <button type="button" class="btn btn-success" onclick="testEditorFeatures()">
                        <i class="fas fa-cog me-1"></i>Test tính năng
                    </button>
                    <button type="button" class="btn btn-warning" onclick="location.reload()">
                        <i class="fas fa-sync me-1"></i>Tải lại
                    </button>
                </div>
            </form>
        </div>

        <!-- Diagnostic Info -->
        <div class="test-section">
            <h3><i class="fas fa-info-circle me-2"></i>Diagnostic Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Browser Info:</h5>
                    <ul id="browser-info">
                        <li>Loading...</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Network Tests:</h5>
                    <ul id="network-info">
                        <li>Testing CDN connectivity...</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="test-section">
            <h3><i class="fas fa-link me-2"></i>Quick Access</h3>
            <div class="d-flex flex-wrap gap-2">
                <a href="admin/news/create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Create News
                </a>
                <a href="admin/news" class="btn btn-secondary">
                    <i class="fas fa-list me-1"></i>Manage News
                </a>
                <a href="debug_ckeditor.php" class="btn btn-info">
                    <i class="fas fa-bug me-1"></i>Debug CKEditor
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- CKEditor Loading with enhanced error handling -->
    <script>
        let ckeditorLoaded = false;
        let fallbackLoaded = false;
        let editorInitialized = false;

        // Enhanced CKEditor loading
        window.CKEditorLoadTimeout = setTimeout(function() {
            if (!ckeditorLoaded) {
                console.warn('⚠️ CKEditor CDN timeout, loading fallback...');
                updateStatus('ckeditor', 'error', 'CDN Timeout');
                loadFallbackEditor();
            }
        }, 8000);

        // Try primary CDN
        const script1 = document.createElement('script');
        script1.src = 'https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js';
        script1.onload = function() {
            clearTimeout(window.CKEditorLoadTimeout);
            ckeditorLoaded = true;
            console.log('✅ CKEditor loaded from primary CDN');
            updateStatus('ckeditor', 'success', 'Loaded v' + (typeof CKEDITOR !== 'undefined' ? CKEDITOR.version : 'Unknown'));
            initializeEditor();
        };
        script1.onerror = function() {
            console.warn('❌ Primary CDN failed, trying backup...');
            loadBackupCDN();
        };
        document.head.appendChild(script1);

        function loadBackupCDN() {
            const script2 = document.createElement('script');
            script2.src = 'https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.22.1/ckeditor.js';
            script2.onload = function() {
                clearTimeout(window.CKEditorLoadTimeout);
                ckeditorLoaded = true;
                console.log('✅ CKEditor loaded from backup CDN');
                updateStatus('ckeditor', 'warning', 'Backup CDN v' + (typeof CKEDITOR !== 'undefined' ? CKEDITOR.version : 'Unknown'));
                initializeEditor();
            };
            script2.onerror = function() {
                console.error('❌ All CDNs failed');
                updateStatus('ckeditor', 'error', 'All CDNs failed');
                loadFallbackEditor();
            };
            document.head.appendChild(script2);
        }

        function loadFallbackEditor() {
            const fallbackScript = document.createElement('script');
            fallbackScript.src = 'assets/js/fallback-editor.js';
            fallbackScript.onload = function() {
                fallbackLoaded = true;
                console.log('✅ Fallback editor loaded');
                updateStatus('fallback', 'success', 'Loaded');
                if (!editorInitialized) {
                    initializeFallbackEditor();
                }
            };
            fallbackScript.onerror = function() {
                console.error('❌ Fallback editor failed to load');
                updateStatus('fallback', 'error', 'Failed to load');
                updateStatus('overall', 'error', 'No editor available');
            };
            document.head.appendChild(fallbackScript);
        }

        function initializeEditor() {
            if (typeof CKEDITOR !== 'undefined' && !editorInitialized) {
                try {
                    CKEDITOR.replace('test-editor', {
                        height: 300,
                        language: 'vi',
                        toolbar: [
                            ['Bold', 'Italic', 'Underline'],
                            ['NumberedList', 'BulletedList'],
                            ['Link', 'Unlink'],
                            ['JustifyLeft', 'JustifyCenter', 'JustifyRight'],
                            ['TextColor', 'BGColor'],
                            ['Source']
                        ],
                        removePlugins: 'elementspath',
                        on: {
                            'instanceReady': function() {
                                editorInitialized = true;
                                updateStatus('overall', 'success', 'CKEditor Ready');
                                console.log('🎉 Test editor initialized!');
                            }
                        }
                    });
                } catch (error) {
                    console.error('CKEditor init error:', error);
                    updateStatus('overall', 'error', 'Init failed: ' + error.message);
                }
            }
        }

        function initializeFallbackEditor() {
            if (typeof FallbackEditor !== 'undefined' && !editorInitialized) {
                try {
                    new FallbackEditor('test-editor');
                    editorInitialized = true;
                    updateStatus('overall', 'warning', 'Fallback Editor Ready');
                    console.log('⚠️ Fallback editor initialized');
                } catch (error) {
                    console.error('Fallback editor error:', error);
                    updateStatus('overall', 'error', 'Fallback failed: ' + error.message);
                }
            }
        }

        function updateStatus(type, status, text) {
            const statusEl = document.getElementById(type + '-status');
            const textEl = document.getElementById(type + '-status-text');

            if (statusEl) {
                statusEl.className = 'status-indicator status-' + status;
            }
            if (textEl) {
                textEl.textContent = text;
            }
        }

        function getEditorContent() {
            let content = '';

            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['test-editor']) {
                content = CKEDITOR.instances['test-editor'].getData();
            } else {
                const textarea = document.getElementById('test-editor');
                content = textarea ? textarea.value : '';
            }

            alert('Nội dung editor:\n\n' + content.substring(0, 500) + (content.length > 500 ? '...' : ''));
        }

        function testEditorFeatures() {
            const features = [];

            if (typeof CKEDITOR !== 'undefined') {
                features.push('✅ CKEditor loaded');
                if (CKEDITOR.instances['test-editor']) {
                    features.push('✅ Editor instance active');
                    features.push('✅ Can get/set content');
                }
            }

            if (typeof FallbackEditor !== 'undefined') {
                features.push('✅ Fallback editor available');
            }

            alert('Tính năng available:\n\n' + features.join('\n'));
        }

        // Initialize browser and network info
        document.addEventListener('DOMContentLoaded', function() {
            // Browser info
            const browserInfo = document.getElementById('browser-info');
            browserInfo.innerHTML = `
                <li>User Agent: ${navigator.userAgent.substring(0, 100)}...</li>
                <li>Platform: ${navigator.platform}</li>
                <li>Language: ${navigator.language}</li>
                <li>Online: ${navigator.onLine ? 'Yes' : 'No'}</li>
            `;

            // Network tests
            const networkInfo = document.getElementById('network-info');

            // Test CDN connectivity
            Promise.all([
                fetch('https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js', {method: 'HEAD'}).then(() => '✅ Primary CDN').catch(() => '❌ Primary CDN'),
                fetch('https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.22.1/ckeditor.js', {method: 'HEAD'}).then(() => '✅ Backup CDN').catch(() => '❌ Backup CDN')
            ]).then(results => {
                networkInfo.innerHTML = results.map(result => `<li>${result}</li>`).join('');
            });

            // Set initial fallback status
            updateStatus('fallback', 'loading', 'Standby');
        });
    </script>
</body>
</html>
