<?php
/**
 * Admin - Create News View
 */
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Thêm Tin Tức Mới</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/admin">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/news/manage">Tin Tức</a></li>
        <li class="breadcrumb-item active">Thêm Mới</li>
    </ol>

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

            <form action="<?php echo SITE_URL; ?>/news/create" method="POST" enctype="multipart/form-data">
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
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Nội Dung <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content" rows="10" required><?php echo isset($_SESSION['form_data']['content']) ? htmlspecialchars($_SESSION['form_data']['content']) : ''; ?></textarea>
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
                    <label class="form-check-label" for="is_published">Xuất Bản Ngay</label>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?php echo SITE_URL; ?>/news/manage" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay Lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu Bài Viết
                    </button>
                </div>
            </form>

            <?php
            // Clear form data after displaying
            if (isset($_SESSION['form_data'])) {
                unset($_SESSION['form_data']);
            }
            ?>
        </div>
    </div>
</div>

<script>
    // Initialize CKEditor
    $(document).ready(function() {
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('content', {
                height: 400,
                removePlugins: 'elementspath',
                resize_enabled: false
            });
        }
    });
</script>
