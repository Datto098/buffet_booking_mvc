# Kế Hoạch Phát Triển Website Đặt Vé Buffet & Đặt Món Nhà Hàng

Dưới đây là kế hoạch chi tiết để xây dựng dự án bằng PHP thuần và MySQL, bao gồm phân tích giao diện, thiết kế database và các bước thực hiện.

## 1. Phân Tích Các Trang Giao Diện (UI - User Interface)

Dựa trên yêu cầu, chúng ta có thể chia giao diện thành 3 khu vực chính: Giao diện cho Khách hàng, Giao diện cho Quản lý, và Giao diện cho Quản trị viên.

### 1.1. Giao Diện Khách Hàng (Client-Side)

1.  **Trang Chủ (`index.php`):**
    * Hiển thị banner khuyến mãi, các món ăn nổi bật, vé buffet phổ biến.
    * Giới thiệu ngắn về nhà hàng, tin tức mới nhất.
    * Thanh tìm kiếm.
<!-- 2.  **Trang Đăng Nhập / Đăng Ký (`login.php`, `register.php`):**
    * Form đăng nhập, đăng ký cho khách hàng.
    * Chức năng "Quên mật khẩu". -->
<!-- 3.  **Trang Hồ Sơ Khách Hàng (`profile.php`):**
    * Xem và chỉnh sửa thông tin cá nhân (tên, email, số điện thoại).
    * Thay đổi mật khẩu. -->
<!-- 4.  **Trang Quản Lý Địa Chỉ (`addresses.php`):**
    * Thêm, sửa, xóa địa chỉ giao hàng.
    * Chọn địa chỉ mặc định. -->
5.  **Trang Thực Đơn / Món Ăn (`menu.php`):**
    * Hiển thị danh sách các món ăn, vé buffet.
    * Bộ lọc theo danh mục (món khai vị, món chính, buffet, ...).
6.  **Trang Chi Tiết Món Ăn (`food_detail.php`):**
    * Hiển thị hình ảnh, mô tả chi tiết, giá tiền, đánh giá của món ăn.
    * Nút "Thêm vào giỏ hàng".
7.  **Trang Giỏ Hàng (`cart.php`):**
    * Liệt kê các món đã chọn, số lượng, thành tiền.
    * Cập nhật số lượng, xóa món ăn khỏi giỏ.
    * Áp dụng mã khuyến mãi.
8.  **Trang Thanh Toán (`checkout.php`):**
    * Chọn địa chỉ giao hàng.
    * Chọn phương thức thanh toán (VD: Tiền mặt khi nhận hàng, Chuyển khoản).
    * Hiển thị tổng tiền cuối cùng và nút "Đặt hàng".
9.  **Trang Xác Nhận Đơn Hàng (`order_success.php`):**
    * Thông báo đặt hàng thành công, hiển thị mã đơn hàng.
10. **Trang Lịch Sử Đơn Hàng (`order_history.php`):**
    * Liệt kê tất cả các đơn hàng đã đặt.
    * Xem chi tiết và theo dõi trạng thái đơn hàng (Đang xử lý, Đang giao, Đã hoàn thành).
11. **Trang Đặt Bàn (`booking.php`):**
    * Form điền thông tin đặt bàn (tên, SĐT, số người, ngày giờ).
12. **Trang Khuyến Mãi (`promotions.php`):**
    * Hiển thị danh sách các chương trình khuyến mãi hiện có.
<!-- 13. **Trang Tin Tức (`news.php`, `news_detail.php`):**
    * Danh sách tin tức và trang chi tiết cho mỗi tin. -->
<!-- 14. **Trang Giới Thiệu (`about.php`):**
    * Thông tin chi tiết về nhà hàng. -->
15. **Trang Kết Quả Tìm Kiếm (`search.php`):**
    * Hiển thị kết quả tìm kiếm món ăn.

### 1.2. Giao Diện Quản Lý (Manager Dashboard - /admin)

* *Cần có một trang đăng nhập riêng cho nhân viên.*
1.  **Dashboard Chính (`/admin/index.php`):**
    * Hiển thị các thống kê nhanh (số món ăn, số danh mục, tin tức mới).
2.  **Quản Lý Món Ăn (`/admin/food_management.php`):**
    * CRUD (Thêm, Xem, Sửa, Xóa) món ăn.
3.  **Quản Lý Danh Mục (`/admin/category_management.php`):**
    * CRUD danh mục món ăn.
4.  **Quản Lý Phụ Mục (`/admin/subcategory_management.php`):**
    * CRUD phụ mục (nếu cần thiết).
5.  **Quản Lý Tin Tức (`/admin/news_management.php`):**
    * CRUD tin tức, bài viết.
6.  **Quản Lý Hồ Sơ Cá Nhân (`/admin/profile.php`):**
    * Sửa thông tin cá nhân của quản lý.
7.  **Xem Thống Kê (`/admin/statistics.php`):**
    * Xem các báo cáo, thống kê được phân quyền.

### 1.3. Giao Diện Quản Trị Viên (Super Admin Dashboard - /superadmin)

* *Sử dụng chung trang đăng nhập với Quản lý nhưng có vai trò cao hơn.*
1.  **Dashboard Chính (`/superadmin/index.php`):**
    * Thống kê tổng quan (doanh thu, đơn hàng mới, người dùng mới).
2.  **Quản Lý Tài Khoản (`/superadmin/user_management.php`):**
    * CRUD tài khoản (Khách hàng, Quản lý, Quản trị viên khác).
    * Phân quyền cho tài khoản.
3.  **Quản Lý Đặt Món (`/superadmin/order_management.php`):**
    * Xem danh sách đơn hàng, chi tiết đơn hàng.
    * Cập nhật trạng thái đơn hàng.
4.  **Quản Lý Đặt Bàn (`/superadmin/booking_management.php`):**
    * Xem, xác nhận, hủy các yêu cầu đặt bàn.
5.  **Quản Lý Bàn (`/superadmin/table_management.php`):**
    * CRUD thông tin các bàn trong nhà hàng (số bàn, sức chứa).
6.  **Quản Lý Thông Tin Nhà Hàng (`/superadmin/restaurant_info.php`):**
    * Chỉnh sửa tên, địa chỉ, SĐT, mô tả nhà hàng.
7.  **Quản Lý Khuyến Mãi (`/superadmin/promotion_management.php`):**
    * CRUD các mã/chương trình khuyến mãi.
8.  **Quản Lý Đánh Giá (`/superadmin/review_management.php`):**
    * Xem, duyệt, hoặc xóa các đánh giá của khách hàng.
9.  **Thống Kê Nâng Cao (`/superadmin/statistics.php`):**
    * Xem báo cáo chi tiết về doanh thu, món ăn bán chạy, ...

---

## 2. Thiết Kế Cơ Sở Dữ Liệu (Database Schema - MySQL)

```sql
-- Bảng lưu trữ tất cả người dùng (Khách hàng, Quản lý, Quản trị viên)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(20),
  `role` ENUM('customer', 'manager', 'admin') NOT NULL DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng lưu địa chỉ của khách hàng
CREATE TABLE `user_addresses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `address_line` TEXT NOT NULL,
  `is_default` BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Bảng danh mục món ăn (VD: Món chính, Tráng miệng, Buffet)
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT
);

-- Bảng phụ mục (VD: Món gà, Món bò trong danh mục Món chính)
CREATE TABLE `sub_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
);

-- Bảng các món ăn và vé buffet
CREATE TABLE `food_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10, 2) NOT NULL,
  `image_url` VARCHAR(255),
  `category_id` INT NOT NULL,
  `sub_category_id` INT,
  `is_available` BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`),
  FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories`(`id`) ON DELETE SET NULL
);

-- Bảng lưu các đơn hàng
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `address_id` INT NOT NULL,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('pending', 'processing', 'delivering', 'completed', 'cancelled') DEFAULT 'pending',
  `payment_method` VARCHAR(50),
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`address_id`) REFERENCES `user_addresses`(`id`)
);

-- Bảng chi tiết các món trong một đơn hàng
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `food_item_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL, -- Giá tại thời điểm đặt hàng
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`food_item_id`) REFERENCES `food_items`(`id`)
);

-- Bảng các bàn trong nhà hàng
CREATE TABLE `tables` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `table_number` VARCHAR(50) NOT NULL,
  `capacity` INT NOT NULL,
  `status` ENUM('available', 'reserved') DEFAULT 'available'
);

-- Bảng lưu thông tin đặt bàn
CREATE TABLE `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT, -- Có thể là khách vãng lai không có tài khoản
  `customer_name` VARCHAR(100) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `table_id` INT,
  `reservation_time` DATETIME NOT NULL,
  `number_of_guests` INT NOT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  `notes` TEXT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`)
);

-- Bảng khuyến mãi
CREATE TABLE `promotions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) UNIQUE NOT NULL,
  `description` TEXT,
  `discount_type` ENUM('percentage', 'fixed_amount') NOT NULL,
  `discount_value` DECIMAL(10, 2) NOT NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `is_active` BOOLEAN DEFAULT TRUE
);

-- Bảng đánh giá của khách hàng
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `food_item_id` INT NOT NULL,
  `rating` TINYINT NOT NULL, -- 1 đến 5 sao
  `comment` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`food_item_id`) REFERENCES `food_items`(`id`) ON DELETE CASCADE
);

-- Bảng tin tức
CREATE TABLE `news` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `author_id` INT NOT NULL,
  `image_url` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`author_id`) REFERENCES `users`(`id`)
);

-- Bảng lưu thông tin nhà hàng (chỉ cần 1 dòng)
CREATE TABLE `restaurant_info` (
  `id` INT PRIMARY KEY DEFAULT 1,
  `name` VARCHAR(255) NOT NULL,
  `address` TEXT NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `about_us_content` TEXT
);
