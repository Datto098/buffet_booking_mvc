<?php
/**
 * Admin News Management View
 */
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản Lý Tin Tức</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/admin">Dashboard</a></li>
        <li class="breadcrumb-item active">Tin Tức</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-newspaper me-1"></i> Danh Sách Tin Tức</div>
            <a href="<?php echo SITE_URL; ?>/news/create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Thêm Tin Tức Mới
            </a>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($news)): ?>
                <div class="alert alert-info">Chưa có tin tức nào. Hãy thêm tin tức mới.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="newsTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tiêu Đề</th>
                                <th>Tác Giả</th>
                                <th>Trạng Thái</th>
                                <th>Ngày Tạo</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td>
                                        <?php if (!empty($item['image_url'])): ?>
                                            <img src="<?php echo SITE_URL; ?>/uploads/news_images/<?php echo htmlspecialchars($item['image_url']); ?>"
                                                class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $authorName = (!empty($item['first_name']) && !empty($item['last_name']))
                                            ? $item['first_name'] . ' ' . $item['last_name']
                                            : 'Admin';
                                        echo htmlspecialchars($authorName);
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $item['is_published'] ? 'success' : 'warning'; ?>">
                                            <?php echo $item['is_published'] ? 'Đã Xuất Bản' : 'Bản Nháp'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/news/detail?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/news/edit?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $item['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal<?php echo $item['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Xác Nhận Xóa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Bạn có chắc chắn muốn xóa tin tức "<strong><?php echo htmlspecialchars($item['title']); ?></strong>"?</p>
                                                        <p class="text-danger">Hành động này không thể hoàn tác!</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                        <form action="<?php echo SITE_URL; ?>/news/delete" method="POST" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                            <button type="submit" class="btn btn-danger">Xóa</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#newsTable').DataTable({
            responsive: true,
            order: [[0, 'desc']]
        });
    });
</script>
