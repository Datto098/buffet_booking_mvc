# 🍽️ Buffet Booking MVC - Hệ Thống Đặt Bàn Buffet & Đặt Món

Một hệ thống quản lý nhà hàng buffet toàn diện được xây dựng bằng PHP thuần và MySQL, hỗ trợ đặt bàn trực tuyến, đặt món ăn, thanh toán và quản lý nhà hàng.

## 📋 Mục Lục

- [Tổng Quan](#tổng-quan)
- [Tính Năng Chính](#tính-năng-chính)
- [Kiến Trúc Hệ Thống](#kiến-trúc-hệ-thống)
- [Cấu Trúc Dự Án](#cấu-trúc-dự-án)
- [Cài Đặt](#cài-đặt)
- [Cấu Hình](#cấu-hình)
- [Hướng Dẫn Sử Dụng](#hướng-dẫn-sử-dụng)
- [API & Endpoints](#api--endpoints)
- [Bảo Mật](#bảo-mật)
- [Đóng Góp](#đóng-góp)

## 🎯 Tổng Quan

Buffet Booking MVC là một hệ thống quản lý nhà hàng buffet hiện đại, được thiết kế để phục vụ ba nhóm người dùng chính:

### 👥 Người Dùng Hệ Thống

1. **Khách Hàng (Customer)**
   - Duyệt thực đơn và đặt món
   - Đặt bàn trực tuyến
   - Theo dõi đơn hàng
   - Quản lý hồ sơ cá nhân
   - Đánh giá món ăn

2. **Quản Lý (Manager)**
   - Quản lý thực đơn và danh mục
   - Xử lý đơn hàng và đặt bàn
   - Quản lý bàn ăn
   - Xem báo cáo và thống kê

3. **Super Admin**
   - Toàn quyền quản lý hệ thống
   - Quản lý người dùng và phân quyền
   - Cấu hình nhà hàng
   - Quản lý khuyến mãi và đánh giá
   - Báo cáo tổng quan

## ✨ Tính Năng Chính

### 🛒 Hệ Thống Đặt Món
- **Thực đơn động**: Hiển thị món ăn theo danh mục với bộ lọc thông minh
- **Giỏ hàng**: Thêm/xóa món, cập nhật số lượng
- **Thanh toán đa dạng**: Tiền mặt, chuyển khoản, VNPay
- **Theo dõi đơn hàng**: Realtime status updates

### 🪑 Hệ Thống Đặt Bàn
- **Đặt bàn trực tuyến**: Form đặt bàn với xác thực thông tin
- **Quản lý bàn**: Tự động gán bàn phù hợp theo số khách
- **Time slot**: Hỗ trợ khung giờ đặt bàn linh hoạt
- **Thông báo tự động**: Email xác nhận và nhắc nhở

### 💰 Hệ Thống Thanh Toán
- **VNPay Integration**: Thanh toán trực tuyến an toàn
- **Multiple payment methods**: COD, Bank Transfer, E-wallet
- **Invoice generation**: Tự động tạo hóa đơn PDF
- **Payment tracking**: Theo dõi trạng thái thanh toán

### 📊 Quản Lý & Báo Cáo
- **Dashboard thống kê**: Revenue, orders, bookings
- **Quản lý người dùng**: CRUD operations với phân quyền
- **Inventory management**: Quản lý kho và món ăn
- **Analytics**: Báo cáo chi tiết về hoạt động kinh doanh

### 🔔 Hệ Thống Thông Báo
- **Real-time notifications**: Thông báo đặt bàn mới
- **Email notifications**: Xác nhận đặt bàn, cập nhật trạng thái
- **Internal messaging**: Hệ thống tin nhắn nội bộ
- **Push notifications**: Thông báo quan trọng

### ⭐ Hệ Thống Đánh Giá
- **Review & Rating**: Đánh giá món ăn với 5 sao
- **Review moderation**: Kiểm duyệt đánh giá
- **Verified reviews**: Xác thực đánh giá từ khách hàng thực
- **Review analytics**: Thống kê đánh giá

## 🏗️ Kiến Trúc Hệ Thống

### 🎨 Pattern MVC
```
Model ←→ Controller ←→ View
   ↓        ↓         ↓
Database  Logic   Templates
```

### 📁 Cấu Trúc Thư Mục
```
buffet_booking_mvc/
├── 📁 config/           # Cấu hình hệ thống
│   ├── config.php       # Cấu hình chung
│   ├── database.php     # Cấu hình database
│   └── vnpay.php        # Cấu hình VNPay
├── 📁 controllers/      # Controllers xử lý logic
│   ├── AdminController.php
│   ├── AuthController.php
│   ├── BookingController.php
│   ├── CartController.php
│   ├── HomeController.php
│   └── ...
├── 📁 models/           # Models xử lý dữ liệu
│   ├── BaseModel.php
│   ├── User.php
│   ├── Food.php
│   ├── Booking.php
│   └── ...
├── 📁 views/            # Templates giao diện
│   ├── admin/           # Giao diện admin
│   ├── superadmin/      # Giao diện super admin
│   ├── customer/        # Giao diện khách hàng
│   └── layouts/         # Layout chung
├── 📁 assets/           # Tài nguyên tĩnh
│   ├── css/
│   ├── js/
│   └── images/
├── 📁 database/         # Database scripts
│   ├── buffet_booking.sql
│   └── migrations/
├── 📁 docs/             # Tài liệu
├── 📁 helpers/          # Helper functions
├── 📁 logs/             # Log files
└── 📁 uploads/          # Uploaded files
```

### 🗄️ Database Schema

#### Core Tables
- **users**: Quản lý người dùng và phân quyền
- **food_items**: Thực đơn và món ăn
- **categories**: Danh mục món ăn
- **orders**: Đơn hàng
- **bookings**: Đặt bàn
- **tables**: Bàn ăn

#### Supporting Tables
- **reviews**: Đánh giá món ăn
- **promotions**: Khuyến mãi
- **notifications**: Thông báo
- **addresses**: Địa chỉ giao hàng
- **payments**: Thanh toán

## 🚀 Cài Đặt

### Yêu Cầu Hệ Thống
- **PHP**: >= 7.4
- **MySQL**: >= 5.7
- **Apache/Nginx**: Web server
- **Composer**: Package manager

### Bước 1: Clone Repository
```bash
git clone https://github.com/username/buffet_booking_mvc.git
cd buffet_booking_mvc
```

### Bước 2: Cài Đặt Dependencies
```bash
composer install
```

### Bước 3: Cấu Hình Database
1. Tạo database MySQL:
```sql
CREATE DATABASE buffet_booking;
```

2. Import database schema:
```bash
mysql -u root -p buffet_booking < database/buffet_booking.sql
```

### Bước 4: Cấu Hình Environment
1. Sao chép file cấu hình:
```bash
cp config/config.example.php config/config.php
```

2. Cập nhật thông tin database trong `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'buffet_booking');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Bước 5: Cấu Hình Web Server
Thiết lập Document Root trỏ đến thư mục dự án hoặc sử dụng XAMPP/WAMP.

## ⚙️ Cấu Hình

### 🔐 VNPay Payment Gateway
Cập nhật thông tin VNPay trong `config/vnpay.php`:
```php
define('VNP_TMNCODE', 'your_terminal_id');
define('VNP_HASHSECRET', 'your_secret_key');
define('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
```

### 📧 Email Configuration
Cấu hình SMTP trong `helpers/mail_helper.php`:
```php
$mail->Host = 'smtp.gmail.com';
$mail->Username = 'your_email@gmail.com';
$mail->Password = 'your_app_password';
```

### 📁 Upload Permissions
Thiết lập quyền ghi cho thư mục uploads:
```bash
chmod 755 uploads/
chmod 755 uploads/food_images/
chmod 755 logs/
```

## 📖 Hướng Dẫn Sử Dụng

### 👤 Tài Khoản Mặc Định

#### Super Admin
- **Email**: `admin@buffetbooking.com`
- **Password**: `admin123`

#### Manager
- **Email**: `manager@buffetbooking.com`
- **Password**: `manager123`

### 🌐 URL Truy Cập

#### Khách Hàng
- **Trang chủ**: `http://localhost/buffet_booking_mvc/`
- **Đăng nhập**: `http://localhost/buffet_booking_mvc/login`
- **Thực đơn**: `http://localhost/buffet_booking_mvc/menu`
- **Đặt bàn**: `http://localhost/buffet_booking_mvc/booking`

#### Admin/Manager
- **Dashboard**: `http://localhost/buffet_booking_mvc/admin/`
- **Quản lý món ăn**: `http://localhost/buffet_booking_mvc/admin/foods`
- **Quản lý đặt bàn**: `http://localhost/buffet_booking_mvc/admin/bookings`
- **Quản lý đơn hàng**: `http://localhost/buffet_booking_mvc/admin/orders`

#### Super Admin
- **Dashboard**: `http://localhost/buffet_booking_mvc/superadmin/`
- **Quản lý người dùng**: `http://localhost/buffet_booking_mvc/superadmin/users`
- **Quản lý đánh giá**: `http://localhost/buffet_booking_mvc/superadmin/reviews`
- **Cấu hình nhà hàng**: `http://localhost/buffet_booking_mvc/superadmin/restaurant`

## 🔌 API & Endpoints

### Authentication Endpoints
```
POST /login              # Đăng nhập
POST /register           # Đăng ký
POST /logout             # Đăng xuất
POST /forgot-password    # Quên mật khẩu
```

### Booking Endpoints
```
GET  /bookings                    # Danh sách đặt bàn
POST /bookings                    # Tạo đặt bàn mới
PUT  /bookings/{id}/status        # Cập nhật trạng thái
GET  /bookings/available-tables   # Bàn trống
```

### Order Endpoints
```
GET  /orders           # Danh sách đơn hàng
POST /orders           # Tạo đơn hàng
PUT  /orders/{id}      # Cập nhật đơn hàng
GET  /orders/{id}/pdf  # Xuất hóa đơn PDF
```

### Menu Endpoints
```
GET /menu              # Thực đơn
GET /menu/categories   # Danh mục
GET /food/{id}         # Chi tiết món ăn
GET /food/{id}/reviews # Đánh giá món ăn
```

## 🔒 Bảo Mật

### 🛡️ Tính Năng Bảo Mật
- **CSRF Protection**: Token bảo vệ mọi form
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Input sanitization
- **Password Hashing**: Bcrypt encryption
- **Session Security**: Secure session handling
- **File Upload Security**: Type và size validation

### 🔐 Phân Quyền
```php
Roles:
├── customer      # Khách hàng - Đặt món, đặt bàn
├── manager       # Quản lý - Admin dashboard
└── super_admin   # Super Admin - Full access
```

### 🚨 Security Headers
```php
// CSRF Token validation
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    throw new Exception('Invalid CSRF token');
}

// Input sanitization
$input = htmlspecialchars(trim($_POST['input']), ENT_QUOTES, 'UTF-8');
```

## 📈 Tính Năng Nổi Bật

### 🎯 Booking System
- **Smart Table Assignment**: Tự động gán bàn phù hợp
- **Time Slot Management**: Quản lý khung giờ đặt bàn
- **Conflict Prevention**: Tránh double booking
- **Guest Management**: Hỗ trợ đặt bàn không cần tài khoản

### 💳 Payment Integration
- **VNPay Gateway**: Thanh toán trực tuyến an toàn
- **Multiple Currencies**: Hỗ trợ VND
- **Payment Tracking**: Theo dõi chi tiết thanh toán
- **Auto Invoice**: Tự động tạo hóa đơn

### 📊 Analytics & Reporting
- **Revenue Dashboard**: Thống kê doanh thu realtime
- **Booking Trends**: Phân tích xu hướng đặt bàn
- **Popular Items**: Món ăn bán chạy
- **Customer Insights**: Phân tích hành vi khách hàng

### 🔔 Notification System
- **Real-time Alerts**: Thông báo tức thì
- **Email Templates**: Template email chuyên nghiệp
- **SMS Integration**: Ready for SMS notifications
- **Push Notifications**: Browser notifications

## 🛠️ Dependencies

### Backend Dependencies
```json
{
  "phpmailer/phpmailer": "^6.10",
  "mpdf/mpdf": "^8.2"
}
```

### Frontend Technologies
- **Bootstrap 5**: UI Framework
- **FontAwesome 6**: Icons
- **jQuery**: DOM manipulation
- **Chart.js**: Data visualization

## 📚 Tài Liệu Kỹ Thuật

### 📋 Available Documentation
- `docs/PLAN.vi.md` - Kế hoạch phát triển (Vietnamese)
- `docs/PLAN.en.md` - Development plan (English)
- `docs/REVIEW_SYSTEM_COMPLETE.md` - Review system documentation
- `docs/PAYMENT_SYSTEM_IMPLEMENTATION.md` - Payment integration guide
- `docs/BOOKING_NOTIFICATION_SYSTEM.md` - Notification system
- `docs/VNPAY_INTEGRATION_COMPLETE.md` - VNPay integration guide

### 🔧 Implementation Guides
- `docs/TIME_SLOT_DISABLE_IMPLEMENTATION.md` - Time slot management
- `docs/AGE_BASED_PRICING_SYSTEM.md` - Pricing system
- `docs/LOGS_MANAGEMENT.md` - Logging system
- `docs/INTERNAL_MESSAGES_GUIDE.md` - Internal messaging

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Error
```bash
# Check database credentials in config/database.php
# Ensure MySQL service is running
sudo service mysql start
```

#### Permission Denied
```bash
# Fix upload directory permissions
chmod 755 uploads/
chown -R www-data:www-data uploads/
```

#### VNPay Payment Issues
```bash
# Verify VNPay credentials in config/vnpay.php
# Check VNPay sandbox environment
# Ensure return URLs are correctly configured
```

## 🤝 Đóng Góp

### 🌟 Contributing Guidelines
1. Fork repository
2. Tạo feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push branch: `git push origin feature/amazing-feature`
5. Mở Pull Request

### 📋 Code Standards
- Follow PSR-4 autoloading
- Use meaningful variable names
- Add comments for complex logic
- Write security-first code
- Test thoroughly before submitting

## 📞 Hỗ Trợ

### 🆘 Get Help
- **Issues**: Tạo issue trên GitHub
- **Email**: support@buffetbooking.com
- **Documentation**: Xem thư mục `docs/`

### 🔄 Updates
- Kiểm tra updates thường xuyên
- Đọc CHANGELOG cho thông tin cập nhật
- Backup data trước khi update

## 📄 License

Dự án này được phát hành dưới MIT License. Xem file `LICENSE` để biết thêm chi tiết.

---

## 🎉 Acknowledgments

- Bootstrap team for amazing UI framework
- VNPay for payment gateway integration
- PHP community for excellent documentation
- Contributors và testers

---

### 📊 Project Statistics

- **Lines of Code**: 50,000+
- **Files**: 200+
- **Database Tables**: 20+
- **Features**: 100+
- **Security Measures**: 15+

---

**Made with ❤️ by Development Team**

*Hệ thống quản lý nhà hàng buffet hiện đại và chuyên nghiệp*
