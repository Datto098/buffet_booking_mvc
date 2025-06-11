# 🔔 Super Admin Notification System

## Tổng quan
Hệ thống notification realtime cho Super Admin được thiết kế để thông báo ngay lập tức khi có:
- Đơn hàng mới
- Booking mới
- Thông báo hệ thống
- Các sự kiện quan trọng khác

## 🚀 Tính năng chính

### 1. **Notification Realtime**
- ✅ Cập nhật tự động mỗi 30 giây
- ✅ Hiển thị badge số lượng notification chưa đọc
- ✅ Âm thanh thông báo khi có notification mới
- ✅ Dropdown notification trong header
- ✅ Sidebar badge indicator

### 2. **Quản lý Notification**
- ✅ Xem tất cả notifications với phân trang
- ✅ Lọc theo loại notification (Order, Booking, System)
- ✅ Lọc chỉ hiển thị chưa đọc
- ✅ Đánh dấu đã đọc (từng cái hoặc tất cả)
- ✅ Xóa notification (từng cái hoặc hàng loạt)
- ✅ Thống kê notification

### 3. **Tích hợp tự động**
- ✅ Tự động tạo notification khi có đơn hàng mới
- ✅ Gửi notification cho tất cả Super Admin
- ✅ Chuyển hướng đến trang chi tiết khi click

## 📱 Giao diện

### Header Notification Dropdown
```
🔔 (5) <- Badge số notification chưa đọc
├── Notifications        [Mark all read]
├── ────────────────────────────────────
├── 🛒 New Order #1001               [New]
│   New order from John Doe - $125.50
│   2 minutes ago               [View Details]
├── ────────────────────────────────────
├── 📅 New Table Booking
│   Table 5 - Jane Smith (4 guests)
│   5 minutes ago               [View Details]
└── ────────────────────────────────────
    [View All Notifications]
```

### Notification Management Page
```
📊 Statistics Cards
├── Total: 25  │ Unread: 5  │ Orders: 15  │ Bookings: 10

🔍 Filters
├── Type: [All/Orders/Bookings/System]
├── ☑️ Show only unread
└── [Clear Filters]

📋 Notification List
├── ☑️ Select All                [Mark Read] [Delete Selected]
├── ────────────────────────────────────────────────────────
├── 🛒 New Order #1001                            [New] [Order]
│   New order from John Doe - $125.50
│   June 11, 2025 at 2:30 PM
│   [👁️ View Order] [✅ Mark Read] [🗑️ Delete]
└── ────────────────────────────────────────────────────────
```

## 🛠️ Cách sử dụng

### Cho Super Admin:

1. **Xem notification mới:**
   - Badge 🔔 (5) sẽ hiển thị số notification chưa đọc
   - Click vào icon 🔔 để xem dropdown
   - Notification mới sẽ có nhãn "New" màu xanh

2. **Quản lý notifications:**
   - Truy cập: `/superadmin/notifications`
   - Sử dụng filter để lọc theo loại
   - Chọn nhiều notification để thao tác hàng loạt

3. **Đánh dấu đã đọc:**
   - Click "Mark Read" trên từng notification
   - Hoặc "Mark all read" để đánh dấu tất cả

4. **Xem chi tiết:**
   - Click "View Order" để chuyển đến trang order
   - Click "View Details" để xem thông tin chi tiết

### Cho Developer:

1. **Tạo notification tự động:**
```php
// Notification sẽ tự động được tạo khi tạo order
$orderId = $orderModel->createOrder($orderData);
// -> Gửi notification cho tất cả Super Admin
```

2. **Tạo notification thủ công:**
```php
$notificationModel = new Notification();
$notificationModel->createNotification([
    'user_id' => $superAdminId,
    'type' => 'new_order',
    'title' => 'New Order #1001',
    'message' => 'New order from John Doe - $125.50',
    'data' => [
        'order_id' => 1001,
        'url' => '/superadmin/orders/view/1001'
    ]
]);
```

## 🔧 Cấu hình

### Database Table: `notifications`
```sql
id          - Auto increment primary key
user_id     - ID của Super Admin nhận notification
type        - Loại: 'new_order', 'new_booking', 'system'
title       - Tiêu đề notification
message     - Nội dung chi tiết
data        - JSON data (order_id, url, etc.)
is_read     - Đã đọc hay chưa (0/1)
created_at  - Thời gian tạo
```

### Notification Types:
- **new_order**: Đơn hàng mới
- **new_booking**: Booking mới
- **system**: Thông báo hệ thống
- **maintenance**: Bảo trì hệ thống
- **promotion**: Khuyến mãi mới

## 🎯 Testing

### 1. Test notification system:
```bash
# Truy cập file test
http://localhost/buffet_booking_mvc/test_notifications.php
```

### 2. Test order notification:
```bash
# Tạo order mới và kiểm tra notification
http://localhost/buffet_booking_mvc/test_order_notification.php
```

### 3. Test realtime:
1. Mở trang Super Admin: `/superadmin/notifications`
2. Tạo order mới từ customer
3. Kiểm tra notification xuất hiện tự động

## 📋 API Endpoints

### For AJAX calls:
```
GET  /superadmin/notifications/unread-count  - Lấy số notification chưa đọc
GET  /superadmin/notifications/recent        - Lấy notification gần đây
POST /superadmin/notifications/mark-read/{id} - Đánh dấu đã đọc
POST /superadmin/notifications/mark-all-read  - Đánh dấu tất cả đã đọc
POST /superadmin/notifications/delete/{id}    - Xóa notification
POST /superadmin/notifications/bulk-action   - Thao tác hàng loạt
```

## ⚡ Performance

### Tối ưu hóa:
- ✅ Polling interval: 30 giây (có thể điều chỉnh)
- ✅ Limit notification dropdown: 10 items
- ✅ Pagination trong trang quản lý: 20 items/page
- ✅ Index database trên user_id, is_read, type
- ✅ Automatic cleanup notifications cũ > 30 ngày

### Monitoring:
- Theo dõi số lượng notification/user
- Log các API calls
- Monitor database performance

## 🔒 Security

### Bảo mật:
- ✅ Chỉ Super Admin mới nhận notification
- ✅ Validate user permission trước khi thao tác
- ✅ CSRF protection cho tất cả POST requests
- ✅ Sanitize input data
- ✅ Rate limiting cho API calls

## 📈 Mở rộng tương lai

### Có thể thêm:
1. **Email notifications** - Gửi email khi có notification quan trọng
2. **Push notifications** - Browser push notifications
3. **Notification templates** - Template cho các loại notification
4. **User preferences** - Cho phép user tùy chỉnh loại notification nhận
5. **Notification scheduling** - Lên lịch gửi notification
6. **Analytics** - Thống kê chi tiết về notifications

---

## 🎉 Hoàn thành!

Hệ thống notification realtime đã sẵn sàng sử dụng. Super Admin sẽ nhận thông báo ngay lập tức khi có:
- Đơn hàng mới từ khách hàng
- Booking bàn mới
- Các sự kiện hệ thống quan trọng

**Next Steps:**
1. Test tất cả tính năng
2. Đào tạo Super Admin cách sử dụng
3. Monitor performance trong thực tế
4. Thu thập feedback để cải thiện
