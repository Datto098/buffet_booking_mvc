<?php

/**
 * Menu Index View
 */

// Ensure SITE_URL is available
$SITE_URL = defined('SITE_URL') ? SITE_URL : 'http://localhost/buffet_booking_mvc';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="hero-title text-luxury">
                Thực Đơn Buffet <br>
                <span class="text-white">Đẳng Cấp Quốc Tế</span>
            </h1>
            <p class="hero-subtitle">
                Khám phá hương vị đa dạng từ khắp thế giới với hơn 200 món ăn tinh tế,
                được chế biến bởi đội ngũ đầu bếp chuyên nghiệp
            </p>
        </div>
    </div>
</section>

<div class="container-fluid section-luxury">

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card-luxury sticky-top" style="top: 20px;">
                <div class="card-header-luxury">
                    <h5 class="mb-0" style="color: #fff !important"><i class="fas fa-filter text-gold"></i> Bộ Lọc</h5>
                </div>
                <div class="card-body-luxury">
                    <form method="GET" action="<?= SITE_URL ?>/menu" id="filterForm">
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label text-luxury">Tìm kiếm</label>
                            <input type="text" class="form-control form-control-luxury" name="search"
                                value="<?= htmlspecialchars($filters['search']) ?>"
                                placeholder="Tên món ăn...">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label class="form-label text-luxury">Danh mục</label>
                            <select class="form-select form-control-luxury" name="category" id="categorySelect">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"
                                        <?= $filters['category'] == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                        (<?= $category['food_count'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Subcategory Filter -->
                        <?php if (!empty($subcategories)): ?>
                            <div class="mb-3">
                                <label class="form-label text-luxury">Danh mục con</label>
                                <select class="form-select form-control-luxury" name="subcategory">
                                    <option value="">Tất cả danh mục con</option>
                                    <?php foreach ($subcategories as $subcategory): ?>
                                        <option value="<?= $subcategory['id'] ?>"
                                            <?= $filters['subcategory'] == $subcategory['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($subcategory['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label text-luxury">Khoảng giá</label>
                            <select class="form-select form-control-luxury" name="price_range">
                                <option value="">Tất cả mức giá</option>
                                <option value="under-50000" <?= $filters['price_range'] == 'under-50000' ? 'selected' : '' ?>>
                                    Dưới 50,000đ
                                </option>
                                <option value="50000-100000" <?= $filters['price_range'] == '50000-100000' ? 'selected' : '' ?>>
                                    50,000đ - 100,000đ
                                </option>
                                <option value="100000-200000" <?= $filters['price_range'] == '100000-200000' ? 'selected' : '' ?>>
                                    100,000đ - 200,000đ
                                </option>
                                <option value="over-200000" <?= $filters['price_range'] == 'over-200000' ? 'selected' : '' ?>>
                                    Trên 200,000đ
                                </option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="mb-3">
                            <label class="form-label text-luxury">Sắp xếp</label>
                            <select class="form-select form-control-luxury" name="sort">
                                <option value="name" <?= $filters['sort'] == 'name' ? 'selected' : '' ?>>
                                    Tên A-Z
                                </option>
                                <option value="price_asc" <?= $filters['sort'] == 'price_asc' ? 'selected' : '' ?>>
                                    Giá tăng dần
                                </option>
                                <option value="price_desc" <?= $filters['sort'] == 'price_desc' ? 'selected' : '' ?>>
                                    Giá giảm dần
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-luxury w-100">
                            <i class="fas fa-search"></i> Áp dụng bộ lọc
                        </button>
                        <a href="<?= SITE_URL ?>/menu" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </form>
                </div>
            </div>
        </div> <!-- Food Items -->
        <div class="col-lg-9">
            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="text-luxury mb-1">Kết quả tìm kiếm</h3>
                    <p class="mb-0 text-muted">
                        Hiển thị <?= count($foods) ?> trong tổng số <?= $totalFoods ?> món ăn
                    </p>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-luxury active" id="gridView">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-outline-luxury" id="listView">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Food Grid -->
            <?php if (empty($foods)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-utensils fa-4x text-gold mb-4"></i>
                    <h4 class="text-luxury">Không tìm thấy món ăn</h4>
                    <p class="text-muted">Thử thay đổi bộ lọc để xem thêm kết quả</p>
                    <a href="<?= SITE_URL ?>/menu" class="btn btn-luxury mt-3">
                        <i class="fas fa-refresh"></i> Xem tất cả món ăn
                    </a>
                </div>
            <?php else: ?>
                <div class="food-grid" id="foodGrid">
                    <?php foreach ($foods as $index => $food): ?>
                        <div class="food-item" style="animation-delay: <?php echo ($index % 12) * 0.1; ?>s">                            <div class="food-image">
                                <img src="<?= !empty($food['image']) ? $SITE_URL . "/uploads/food_images/" . htmlspecialchars($food['image']) : $SITE_URL . '/assets/images/food-placeholder.svg' ?>"
                                    alt="<?= htmlspecialchars($food['name']) ?>"
                                    class="card-img-luxury">
                                <div class="food-badge">
                                    <i class="fas fa-star"></i> Premium
                                </div>
                            </div>
                            <div class="food-content">
                                <div class="food-category">
                                    <?= htmlspecialchars($food['category_name']) ?>
                                </div>
                                <h3 class="food-title"><?= htmlspecialchars($food['name']) ?></h3>
                                <p class="food-description">
                                    <?= htmlspecialchars(substr($food['description'], 0, 120)) ?>
                                    <?= strlen($food['description']) > 120 ? '...' : '' ?>
                                </p>
                                <div class="food-price">
                                    <span class="price-current">
                                        <?= number_format($food['price'], 0, ',', '.') ?>đ
                                    </span>
                                </div>
                                <div class="d-flex gap-2">                                    <button class="btn btn-luxury add-to-cart-btn flex-grow-1"
                                        data-food-id="<?= $food['id'] ?>"
                                        data-food-name="<?= htmlspecialchars($food['name']) ?>"
                                        data-food-price="<?= $food['price'] ?>"
                                        data-food-image="<?= !empty($food['image']) ? $SITE_URL . '/uploads/food_images/' . htmlspecialchars($food['image']) : $SITE_URL . '/assets/images/food-placeholder.svg' ?>">
                                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                    </button>
                                    <a href="<?= SITE_URL ?>/food/detail/<?= $food['id'] ?>"
                                        class="btn btn-outline-luxury">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?> <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Menu pagination" class="mt-5">
                    <ul class="pagination justify-content-center pagination-luxury">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= buildPaginationUrl($currentPage - 1, $filters) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= buildPaginationUrl($i, $filters) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= buildPaginationUrl($currentPage + 1, $filters) ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Add Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-luxury">
            <div class="modal-header border-gold">
                <h5 class="modal-title text-luxury">
                    <i class="fas fa-shopping-cart text-gold"></i> Thêm vào giỏ hàng
                </h5>
                <button type="button" class="btn-close btn-close-luxury" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">
                        <img id="modalFoodImage" src="" class="img-fluid rounded card-img-luxury" alt="">
                    </div>
                    <div class="col-8">
                        <h6 id="modalFoodName" class="text-luxury"></h6>
                        <p class="text-gold h5" id="modalFoodPrice"></p>

                        <div class="mb-3">
                            <label class="form-label text-luxury">Số lượng</label>
                            <div class="input-group">
                                <button class="btn btn-outline-luxury" type="button" id="decreaseQty">-</button>
                                <input type="number" class="form-control text-center" id="modalQuantity" value="1" min="1">
                                <button class="btn btn-outline-luxury" type="button" id="increaseQty">+</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong class="text-luxury">Tổng: <span id="modalTotal" class="text-gold"></span></strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-gold">
                <button type="button" class="btn btn-outline-luxury" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-luxury" id="confirmAddToCart">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                </button>
            </div>
        </div>
    </div>
</div>
</button>
</div>
</div>
</div>
</div>

<?php
// Helper function for pagination URLs
function buildPaginationUrl($page, $filters)
{
    $params = array_filter([
        'p' => $page,
        'category' => $filters['category'] ?: null,
        'subcategory' => $filters['subcategory'] ?: null,
        'search' => $filters['search'] ?: null,
        'sort' => $filters['sort'] !== 'name' ? $filters['sort'] : null,
        'price_range' => $filters['price_range'] ?: null
    ]);

    return SITE_URL . '/menu?' . http_build_query($params);
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add to cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const foodId = this.dataset.foodId;
                const foodName = this.dataset.foodName;
                const foodPrice = this.dataset.foodPrice;

                // Show quick add modal
                document.getElementById('modalFoodName').textContent = foodName;
                document.getElementById('modalFoodPrice').textContent = formatPrice(foodPrice);
                document.getElementById('modalTotal').textContent = formatPrice(foodPrice);

                // Set food data
                document.getElementById('confirmAddToCart').dataset.foodId = foodId;
                document.getElementById('confirmAddToCart').dataset.foodPrice = foodPrice;

                // Show modal
                new bootstrap.Modal(document.getElementById('quickAddModal')).show();
            });
        });

        // Quantity controls
        document.getElementById('increaseQty').addEventListener('click', function() {
            const input = document.getElementById('modalQuantity');
            input.value = parseInt(input.value) + 1;
            updateModalTotal();
        });

        document.getElementById('decreaseQty').addEventListener('click', function() {
            const input = document.getElementById('modalQuantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateModalTotal();
            }
        });

        document.getElementById('modalQuantity').addEventListener('input', updateModalTotal);

        function updateModalTotal() {
            const quantity = parseInt(document.getElementById('modalQuantity').value) || 1;
            const price = parseFloat(document.getElementById('confirmAddToCart').dataset.foodPrice);
            const total = quantity * price;
            document.getElementById('modalTotal').textContent = formatPrice(total);
        }

        // Confirm add to cart
        document.getElementById('confirmAddToCart').addEventListener('click', function() {
            const foodId = this.dataset.foodId;
            const quantity = parseInt(document.getElementById('modalQuantity').value);

            addToCart(foodId, quantity).then(() => {
                bootstrap.Modal.getInstance(document.getElementById('quickAddModal')).hide();
                showToast('Đã thêm vào giỏ hàng', 'success');
            });
        });

        // Filter form auto-submit
        document.getElementById('categorySelect').addEventListener('change', function() {
            // Clear subcategory when category changes
            const subcategorySelect = document.querySelector('select[name="subcategory"]');
            if (subcategorySelect) {
                subcategorySelect.value = '';
            }
            document.getElementById('filterForm').submit();
        });

        // View toggle
        document.getElementById('gridView').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('listView').classList.remove('active');
            document.getElementById('foodGrid').className = 'row';
        });

        document.getElementById('listView').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('gridView').classList.remove('active');
            document.getElementById('foodGrid').className = 'row list-view';
        });
    });

    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }
</script>

<style>
    .food-card {
        transition: transform 0.2s ease-in-out;
    }

    .food-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .favorite-btn {
        backdrop-filter: blur(10px);
    }

    .list-view .food-item {
        width: 100%;
    }

    .list-view .food-card {
        flex-direction: row;
    }

    .list-view .food-card img {
        width: 200px;
        height: 150px;
    }

    @media (max-width: 768px) {
        .list-view .food-card {
            flex-direction: column;
        }

        .list-view .food-card img {
            width: 100%;
            height: 200px;
        }
    }
</style>
