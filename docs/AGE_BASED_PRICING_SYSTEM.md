# HỆ THỐNG GIÁ THEO ĐỘ TUỔI - BUFFET BOOKING

## Tổng quan
Hệ thống đã được cập nhật để hỗ trợ tính giá theo độ tuổi thay vì giá cố định cho tất cả khách:

### Bảng giá mới:
- **Người lớn (từ 18 tuổi):** 299,000đ (trưa) / 399,000đ (tối) / 449,000đ (cuối tuần)
- **Trẻ em (11-17 tuổi):** 199,000đ (tất cả khung giờ)
- **Trẻ em (6-10 tuổi):** 99,000đ (tất cả khung giờ)
- **Trẻ em (0-5 tuổi):** Miễn phí

## Cấu trúc Database

### 1. Bảng `age_based_pricing`
```sql
CREATE TABLE age_based_pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    age_group VARCHAR(50) NOT NULL,
    min_age INT NOT NULL,
    max_age INT NOT NULL,
    lunch_price DECIMAL(10,2) NOT NULL,
    dinner_price DECIMAL(10,2) NOT NULL,
    weekend_price DECIMAL(10,2) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 2. Cập nhật bảng `bookings`
Đã thêm các cột:
- `adult_count`: Số người lớn
- `children_0_5_count`: Số trẻ 0-5 tuổi
- `children_6_10_count`: Số trẻ 6-10 tuổi
- `children_11_17_count`: Số trẻ 11-17 tuổi
- `guest_breakdown`: Chi tiết khách theo độ tuổi (JSON)

### 3. Bảng `booking_age_breakdown`
```sql
CREATE TABLE booking_age_breakdown (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    age_group VARCHAR(50) NOT NULL,
    count INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);
```

## Giao diện người dùng

### 1. Form đặt bàn
Thay vì chọn "Số lượng khách" đơn giản, giờ có 4 dropdown riêng biệt:

```html
<!-- Người lớn -->
<select name="adult_count" required>
    <option value="">Chọn</option>
    <option value="0">0 người</option>
    <option value="1">1 người</option>
    ...
</select>

<!-- Trẻ em 11-17 tuổi -->
<select name="children_11_17_count">
    <option value="">Chọn</option>
    <option value="0">0 trẻ</option>
    ...
</select>

<!-- Tương tự cho các nhóm tuổi khác -->
```

### 2. Sidebar bảng giá
Hiển thị giá chi tiết theo từng nhóm tuổi với icon và badge màu sắc:
- Người lớn: Icon user, badge xanh lá/xanh dương/vàng
- Trẻ em 11-17: Icon child, badge xanh nước biển
- Trẻ em 6-10: Icon baby, badge xanh lá
- Trẻ em 0-5: Icon baby-carriage, badge xám (miễn phí)

### 3. Thông tin thanh toán
Hiển thị breakdown chi tiết:
```
Chi tiết giá:
- 2 người lớn × 299,000đ = 598,000đ
- 1 trẻ 11-17 tuổi × 199,000đ = 199,000đ
- 1 trẻ 6-10 tuổi × 99,000đ = 99,000đ
- 1 trẻ 0-5 tuổi × Miễn phí = 0đ
─────────────────────────────────
Tổng cộng: 896,000đ
```

## Logic tính toán

### JavaScript (Frontend)
```javascript
function updatePaymentInfo() {
    const adultCount = parseInt(document.getElementById('adult_count').value) || 0;
    const children11_17Count = parseInt(document.getElementById('children_11_17_count').value) || 0;
    const children6_10Count = parseInt(document.getElementById('children_6_10_count').value) || 0;
    const children0_5Count = parseInt(document.getElementById('children_0_5_count').value) || 0;

    // Xác định giá theo khung giờ và ngày
    const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
    let adultPrice, childPrice11_17, childPrice6_10, childPrice0_5;

    if (isWeekend) {
        adultPrice = 449000;
        // Trẻ em giá cố định
    } else if (timeSlot >= 11 && timeSlot < 15) {
        adultPrice = 299000; // Lunch
    } else {
        adultPrice = 399000; // Dinner
    }

    // Tính tổng
    const totalAmount = (adultCount * adultPrice) +
                       (children11_17Count * 199000) +
                       (children6_10Count * 99000) +
                       (children0_5Count * 0);
}
```

### PHP (Backend)
```php
private function calculatePaymentAmounts($bookingDate, $bookingTime, $bookingData) {
    // Lấy pricing từ database
    $stmt = $db->prepare("SELECT * FROM age_based_pricing ORDER BY min_age");
    $agePricing = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalAmount = 0;

    foreach ($agePricing as $pricing) {
        $count = 0;
        switch ($pricing['age_group']) {
            case 'adults':
                $count = $bookingData['adult_count'] ?? 0;
                break;
            case 'children_11_17':
                $count = $bookingData['children_11_17_count'] ?? 0;
                break;
            // ... các nhóm tuổi khác
        }

        if ($count > 0) {
            $pricePerPerson = $this->getPriceByTimeSlot($pricing, $isWeekend, $timeSlot);
            $totalAmount += $count * $pricePerPerson;
        }
    }

    return [
        'total_amount' => $totalAmount,
        'prepaid_amount' => round($totalAmount * 0.15),
        'remaining_amount' => $totalAmount - round($totalAmount * 0.15)
    ];
}
```

## Validation

### 1. Frontend Validation
- Ít nhất 1 người lớn phải có để đặt bàn
- Tổng số khách không quá 20 người
- Cập nhật tổng số khách real-time

### 2. Backend Validation
```php
// Validate age group counts
if ($adultCount < 0 || $adultCount > 20) {
    $errors[] = 'Số lượng người lớn không hợp lệ';
}

// Must have at least one adult
if ($adultCount == 0) {
    $errors[] = 'Phải có ít nhất 1 người lớn để đặt bàn';
}
```

## Files đã cập nhật

### 1. Database Setup
- `setup_age_based_pricing.php`: Script tạo bảng và dữ liệu mới

### 2. Controllers
- `BookingController.php`:
  - Cập nhật `handleBookingSubmission()` để xử lý age group data
  - Cập nhật `calculatePaymentAmounts()` cho tính toán mới
  - Thêm validation cho age groups

### 3. Models
- `Booking.php`:
  - Cập nhật `createBookingFromController()` để lưu age group data

### 4. Views
- `views/customer/booking/index.php`:
  - Thay thế dropdown "Số lượng khách" bằng 4 dropdown riêng
  - Cập nhật sidebar pricing với bảng giá mới
  - Cập nhật payment info card với breakdown chi tiết
  - Cập nhật JavaScript cho tính toán mới

## Testing

### 1. Script test tự động
```bash
http://localhost/buffet_booking_mvc/test_age_based_pricing.php
```

### 2. Test cases thủ công
1. **Gia đình 4 người (2 adults, 1 teen, 1 kid) - Trưa thường:**
   - Expected: 2×299k + 1×199k + 1×99k = 896,000đ

2. **Cặp đôi (2 adults) - Tối thường:**
   - Expected: 2×399k = 798,000đ

3. **Gia đình 5 người với baby - Cuối tuần:**
   - Expected: 2×449k + 1×199k + 1×99k + 1×0 = 1,196,000đ

## Lưu ý quan trọng

### 1. Tương thích ngược
- Trường `party_size` vẫn được duy trì và tự động tính từ tổng các age groups
- Code cũ vẫn hoạt động bình thường

### 2. Business Rules
- **Bắt buộc có ít nhất 1 người lớn** để đặt bàn
- Trẻ em 0-5 tuổi hoàn toàn miễn phí
- Giá trẻ em không thay đổi theo khung giờ (trừ cuối tuần)

### 3. Email & PDF
- Email confirmation sẽ hiển thị breakdown chi tiết
- PDF booking detail sẽ bao gồm thông tin age groups

## Tính năng mở rộng

### 1. Admin Management
- Trang quản lý pricing theo age group
- Thống kê booking theo age groups
- Báo cáo doanh thu chi tiết

### 2. Promotion Integration
- Discount cho family packages
- Special rates cho groups có nhiều trẻ em
- Birthday promotions

### 3. Advanced Features
- Age verification cho bookings
- Group booking packages
- Loyalty points theo age groups

## Troubleshooting

### Lỗi thường gặp:
1. **"Phải có ít nhất 1 người lớn"**: User chưa chọn adult count
2. **JavaScript không tính toán**: Kiểm tra browser console
3. **Database error**: Chạy lại setup_age_based_pricing.php
4. **Payment calculation sai**: Kiểm tra age_based_pricing table

### Debug tips:
- Sử dụng test script để verify calculations
- Kiểm tra browser developer tools
- Check database có đủ age group data không

## Kết luận

Hệ thống giá theo độ tuổi đã được implement hoàn chỉnh với:
- ✅ 4 nhóm tuổi riêng biệt với giá khác nhau
- ✅ Giao diện user-friendly với breakdown chi tiết
- ✅ Validation đầy đủ và tương thích ngược
- ✅ Test scripts và documentation

Hệ thống sẵn sàng cho production và có thể mở rộng thêm các tính năng nâng cao!
