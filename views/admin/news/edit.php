<!-- Page Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2">Chỉnh Sửa Tin Tức</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/news">Tin Tức</a></li>
                <li class="breadcrumb-item active">Chỉnh Sửa</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-newspaper me-1"></i> Thông Tin Bài Viết
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?><form action="<?php echo SITE_URL; ?>/admin/news/edit/<?php echo $news_item['id']; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="title" class="form-label">Tiêu Đề <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required
                        value="<?php echo htmlspecialchars($news_item['title'] ?? ''); ?>">
                </div>

                <div class="col-md-4">
                    <label for="image" class="form-label">Hình Ảnh</label> <?php if (!empty($news_item['image_url'])): ?>
                        <div class="mb-2">
                            <img src="<?php echo SITE_URL; ?>/uploads/news_images/<?php echo $news_item['image_url']; ?>"
                                alt="Current image" class="img-thumbnail" style="max-height: 100px;">
                            <small class="d-block text-muted">Hình ảnh hiện tại</small>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <small class="text-muted">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF. <?php echo !empty($news_item['image']) ? 'Chọn file mới để thay đổi.' : ''; ?></small>
                </div>
            </div>

            <div class="mb-3">
                <label for="excerpt" class="form-label">Tóm Tắt</label>
                <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($news_item['excerpt'] ?? ''); ?></textarea>
                <small class="text-muted">Mô tả ngắn gọn về bài viết, sẽ hiển thị ở trang danh sách tin tức.</small>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">
                    Nội Dung <span class="text-danger">*</span>
                    <small class="text-muted">(Sử dụng Rich Text Editor)</small>
                </label>
                <!-- Quill Editor Container -->
                <div id="quill-editor" style="height:400px;background:#fff;"></div>
                <textarea class="form-control" id="content" name="content" rows="12" required style="display:none;"><?php echo htmlspecialchars($news_item['content'] ?? ''); ?></textarea>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Sử dụng rich text editor Quill để định dạng văn bản, thêm liên kết, bảng, và nhiều hơn nữa.
                </small>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title"
                        value="<?php echo htmlspecialchars($news_item['meta_title'] ?? ''); ?>">
                    <small class="text-muted">Tiêu đề cho SEO, nếu để trống sẽ sử dụng tiêu đề bài viết.</small>
                </div>

                <div class="col-md-6">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?php echo htmlspecialchars($news_item['meta_description'] ?? ''); ?></textarea>
                    <small class="text-muted">Mô tả cho SEO, nếu để trống sẽ sử dụng tóm tắt bài viết.</small>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1"
                    <?php echo (!empty($news_item['is_published']) && $news_item['is_published']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_published">Xuất Bản Ngay</label>
            </div>

            <div class="d-flex justify-content-between">
                <a href="<?php echo SITE_URL; ?>/admin/news" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay Lại
                </a> <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Cập Nhật Bài Viết
                </button>
            </div>
        </form>
    </div>
</div>

<!-- QuillJS CDN & Image Resize Module -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
<script>
    window.SITE_URL = '<?= SITE_URL ?>';
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo Quill
        var quill = new Quill('#quill-editor', {
            theme: 'snow',
            placeholder: 'Nhập nội dung chi tiết của bài viết...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['blockquote', 'code-block'],
                    [{ 'color': [] }, { 'background': [] }],
                    ['clean']
                ],
                imageResize: {
                    // module options (default)
                }
            }
        });

        // Nếu có dữ liệu cũ thì set vào Quill
        var oldContent = document.getElementById('content').value;
        if (oldContent && oldContent.trim() && oldContent.trim() !== '<p><br></p>') {
            quill.root.innerHTML = oldContent;
        }

        // Khi submit form, lấy HTML từ Quill gán vào textarea content
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Lấy HTML từ Quill
                var html = quill.root.innerHTML.trim();
                if (!html || html === '<p><br></p>') {
                    e.preventDefault();
                    alert('Vui lòng nhập nội dung bài viết!');
                    return false;
                }
                // Gán lại value cho textarea (xóa hết khoảng trắng đầu/cuối)
                document.getElementById('content').value = html;

                // Show loading spinner
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang cập nhật...';
                }
            });
        }
    });
</script>
