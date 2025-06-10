<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
    <title>Create News - Admin</title>
    <?php require_once 'views/admin/layouts/header.php'; ?>
</head>
<body class="admin-page">
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/admin/layouts/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">Thêm Tin Tức Mới</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/news">Tin Tức</a></li>
                                <li class="breadcrumb-item active">Thêm Mới</li>
                            </ol>
                        </nav>
                    </div>                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-newspaper me-1"></i> Thông Tin Bài Viết
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

            <form action="<?php echo SITE_URL; ?>/admin/news/create" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Tiêu Đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required
                               value="<?php echo isset($_SESSION['form_data']['title']) ? htmlspecialchars($_SESSION['form_data']['title']) : ''; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="image" class="form-label">Hình Ảnh</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label">Tóm Tắt</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo isset($_SESSION['form_data']['excerpt']) ? htmlspecialchars($_SESSION['form_data']['excerpt']) : ''; ?></textarea>
                    <small class="text-muted">Mô tả ngắn gọn về bài viết, sẽ hiển thị ở trang danh sách tin tức.</small>
                </div>                <div class="mb-3">
                    <label for="content" class="form-label">
                        Nội Dung <span class="text-danger">*</span>
                        <small class="text-muted">(Sử dụng Rich Text Editor)</small>
                    </label>
                    <textarea class="form-control" id="content" name="content" rows="12" required
                              placeholder="Nhập nội dung chi tiết của bài viết..."><?php echo isset($_SESSION['form_data']['content']) ? htmlspecialchars($_SESSION['form_data']['content']) : ''; ?></textarea>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Sử dụng rich text editor để định dạng văn bản, thêm liên kết, bảng, và nhiều hơn nữa.
                    </small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                               value="<?php echo isset($_SESSION['form_data']['meta_title']) ? htmlspecialchars($_SESSION['form_data']['meta_title']) : ''; ?>">
                        <small class="text-muted">Tiêu đề cho SEO, nếu để trống sẽ sử dụng tiêu đề bài viết.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?php echo isset($_SESSION['form_data']['meta_description']) ? htmlspecialchars($_SESSION['form_data']['meta_description']) : ''; ?></textarea>
                        <small class="text-muted">Mô tả cho SEO, nếu để trống sẽ sử dụng tóm tắt bài viết.</small>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1"
                           <?php echo (isset($_SESSION['form_data']['is_published']) && $_SESSION['form_data']['is_published']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_published">Xuất Bản Ngay</label>                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?php echo SITE_URL; ?>/admin/news" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay Lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu Bài Viết
                    </button>                </div>
            </form>

            <?php
            // Clear form data after displaying
            if (isset($_SESSION['form_data'])) {
                unset($_SESSION['form_data']);
            }
            ?>
                    </div>
                </div>

            </main>
        </div>
    </div>

<script>
    // Set up site URL for admin functions
    window.SITE_URL = '<?= SITE_URL ?>';

    // Initialize news create form
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded, initializing CKEditor...');

        // Initialize CKEditor for content textarea
        if (typeof CKEDITOR !== 'undefined') {
            console.log('CKEditor is available, replacing textarea...');

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
                        console.log('CKEditor is ready for news creation!');

                        // Show success notification
                        const notification = document.createElement('div');
                        notification.className = 'alert alert-success alert-dismissible fade show';
                        notification.style.position = 'fixed';
                        notification.style.top = '20px';
                        notification.style.right = '20px';
                        notification.style.zIndex = '9999';
                        notification.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>
                            Rich Text Editor đã sẵn sàng để tạo nội dung!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.body.appendChild(notification);

                        // Auto-hide notification after 3 seconds
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.remove();
                            }
                        }, 3000);
                    }
                }
            });
        } else {
            console.error('CKEditor not loaded - fallback to plain textarea');

            // Show error notification
            const errorNotification = document.createElement('div');
            errorNotification.className = 'alert alert-warning alert-dismissible fade show';
            errorNotification.style.position = 'fixed';
            errorNotification.style.top = '20px';
            errorNotification.style.right = '20px';
            errorNotification.style.zIndex = '9999';
            errorNotification.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                Rich Text Editor không tải được. Đang sử dụng textarea thông thường.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(errorNotification);
        }

        // Initialize form validation and submission
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Update CKEditor content before submit
                if (CKEDITOR.instances.content) {
                    CKEDITOR.instances.content.updateElement();

                    // Validate content is not empty
                    const content = CKEDITOR.instances.content.getData().trim();
                    if (!content || content === '<p></p>' || content === '<p>&nbsp;</p>') {
                        e.preventDefault();
                        alert('Vui lòng nhập nội dung bài viết!');
                        return false;
                    }
                }

                // Validate title
                const title = document.getElementById('title').value.trim();
                if (!title) {
                    e.preventDefault();
                    alert('Vui lòng nhập tiêu đề bài viết!');
                    document.getElementById('title').focus();
                    return false;
                }

                // Show loading spinner
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang lưu...';
                }
            });
        }
    });
</script>

<?php require_once 'views/admin/layouts/footer.php'; ?>
</body>
</html>
