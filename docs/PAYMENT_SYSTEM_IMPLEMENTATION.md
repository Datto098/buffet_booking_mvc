# HỆ THỐNG THANH TOÁN BUFFET BOOKING - HƯỚNG DẪN HOÀN CHỈNH

## Tổng quan
Hệ thống thanh toán cho Buffet Booking cho phép khách hàng:
- Trả trước 15% khi đặt bàn để xác nhận
- Trả phần còn lại (85%) khi tới nhà hàng
- Theo dõi thông tin thanh toán chi tiết

## Cấu trúc Database

### 1. Bảng `buffet_pricing`
```sql
CREATE TABLE buffet_pricing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lunch_price DECIMAL(10,2) DEFAULT 299000,
    dinner_price DECIMAL(10,2) DEFAULT 399000,
    weekend_price DECIMAL(10,2) DEFAULT 449000,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 2. Cập nhật bảng `bookings`
Đã thêm các cột:
- `total_amount`: Tổng tiền buffet
- `prepaid_amount`: Số tiền trả trước (15%)
- `remaining_amount`: Số tiền còn lại
- `payment_status`: Trạng thái thanh toán (pending, partial, completed, cancelled)

### 3. Bảng `payment_transactions`
```sql
CREATE TABLE payment_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT,
    transaction_type ENUM('prepayment', 'final_payment', 'refund'),
    amount DECIMAL(10,2),
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed', 'cancelled'),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);
```

## Quy tắc tính giá

### Khung giờ và giá
1. **Trưa (11:00-15:00)**: 299,000 VNĐ/người
2. **Tối (17:00-21:00)**: 399,000 VNĐ/người
3. **Cuối tuần (Thứ 7, Chủ nhật)**: 449,000 VNĐ/người

### Cách tính toán
```javascript
// Xác định giá theo ngày và giờ
const dayOfWeek = bookingDate.getDay(); // 0 = Sunday, 6 = Saturday
const timeSlot = parseInt(time.split(':')[0]);

if (dayOfWeek === 0 || dayOfWeek === 6) {
    pricePerPerson = 449000; // Weekend
} else if (timeSlot >= 11 && timeSlot < 15) {
    pricePerPerson = 299000; // Lunch
} else if (timeSlot >= 17 && timeSlot < 21) {
    pricePerPerson = 399000; // Dinner
} else {
    pricePerPerson = 399000; // Default
}

// Tính toán thanh toán
const totalAmount = pricePerPerson * partySize;
const prepaidAmount = Math.round(totalAmount * 0.15); // 15%
const remainingAmount = totalAmount - prepaidAmount;
```

## Các file đã được cập nhật

### 1. Database Setup
- `setup_payment_system.php`: Script tạo bảng và dữ liệu mẫu

### 2. Controllers
- `BookingController.php`:
  - Thêm hàm `calculatePaymentAmounts()`
  - Cập nhật `handleBookingSubmission()` để tính toán payment
  - Cập nhật email confirmation với thông tin thanh toán

### 3. Models
- `Booking.php`:
  - Cập nhật `createBookingFromController()` để lưu thông tin payment

### 4. Views
- `views/customer/booking/index.php`:
  - Thêm sidebar hiển thị bảng giá
  - Thêm card thông tin thanh toán
  - Thêm JavaScript tính toán real-time

## Luồng hoạt động

### 1. Khách hàng đặt bàn
1. Chọn ngày, giờ, số khách, địa chỉ
2. JavaScript tự động tính giá và hiển thị:
   - Giá/người
   - Tổng tiền
   - Số tiền trả trước (15%)
   - Số tiền còn lại
3. Submit form đặt bàn

### 2. Xử lý backend
1. `BookingController::handleBookingSubmission()` nhận dữ liệu
2. Gọi `calculatePaymentAmounts()` để tính toán
3. Lưu booking với thông tin payment vào database
4. Gửi email xác nhận có thông tin thanh toán

### 3. Email confirmation
Email sẽ bao gồm:
- Thông tin đặt bàn cơ bản
- Chi tiết thanh toán:
  - Tổng tiền
  - Cần trả trước (15%)
  - Còn lại khi tới ăn
- Lưu ý về thanh toán

## Cách test hệ thống

### 1. Chạy script test
```
http://localhost/buffet_booking_mvc/test_payment_system.php
```

### 2. Test thực tế
1. Truy cập trang đặt bàn: `http://localhost/buffet_booking_mvc/index.php?page=booking`
2. Điền thông tin và chọn các option khác nhau
3. Quan sát thông tin thanh toán tự động cập nhật
4. Submit và kiểm tra email + database

### 3. Kiểm tra database
```sql
-- Xem bookings với thông tin payment
SELECT customer_name, booking_date, booking_time, guest_count,
       total_amount, prepaid_amount, remaining_amount, payment_status
FROM bookings
ORDER BY created_at DESC LIMIT 10;

-- Xem bảng giá
SELECT * FROM buffet_pricing;
```

## Các tính năng bổ sung có thể thêm

### 1. Payment Gateway Integration
- Tích hợp VNPay/MoMo để thanh toán online
- Xử lý callback và cập nhật trạng thái

### 2. Admin Management
- Trang quản lý giá buffet
- Theo dõi doanh thu theo payment
- Báo cáo thanh toán

### 3. Customer Features
- Lịch sử thanh toán
- In hóa đơn
- Hoàn tiền online

## Ghi chú kỹ thuật

### JavaScript Functions
- `updatePaymentInfo()`: Tính toán khi thay đổi form
- `updatePaymentUI()`: Cập nhật hiển thị
- `formatCurrency()`: Format tiền tệ Việt Nam

### PHP Functions
- `calculatePaymentAmounts()`: Tính toán payment amounts
- Database queries được chuẩn hóa với prepared statements

### Security
- Validation đầy đủ cho tất cả input
- Escape HTML output
- Protected against SQL injection

## Troubleshooting

### Lỗi thường gặp
1. **Database connection error**: Kiểm tra config database
2. **Payment không tính**: Kiểm tra bảng `buffet_pricing` có dữ liệu
3. **JavaScript không chạy**: Kiểm tra console browser
4. **Email không gửi**: Kiểm tra mail configuration

### Debug
- Kiểm tra `logs/` folder cho error logs
- Sử dụng browser developer tools
- Test từng function riêng lẻ

## Kết luận

Hệ thống payment đã được tích hợp hoàn chỉnh với:
- ✅ Tính toán giá tự động theo khung giờ
- ✅ Hiển thị thông tin thanh toán real-time
- ✅ Lưu trữ database đầy đủ
- ✅ Email confirmation với payment info
- ✅ Test script để verification

Hệ thống sẵn sàng cho việc tích hợp payment gateway và các tính năng nâng cao khác.
