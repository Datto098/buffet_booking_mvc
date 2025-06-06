# Tests and Scripts Guide

Tài liệu này giải thích cách tổ chức và sử dụng các file test và script trong dự án Buffet Booking MVC.

## Cấu trúc thư mục

### Tests

Thư mục `tests` chứa các file kiểm thử cho từng chức năng của hệ thống, được chia thành các thư mục con:

- `tests/booking/`: Tests cho hệ thống đặt bàn
- `tests/database/`: Tests cho cơ sở dữ liệu
- `tests/menu/`: Tests cho chức năng menu và món ăn
- `tests/news/`: Tests cho chức năng tin tức
- `tests/orders/`: Tests cho chức năng đặt món và quản lý đơn hàng
- `tests/utils/`: Tests tiện ích và các test chung

### Scripts

Thư mục `scripts` chứa các script thực hiện các tác vụ cụ thể, được chia thành các thư mục con:

- `scripts/database/`: Scripts liên quan đến cơ sở dữ liệu
- `scripts/fixes/`: Scripts sửa lỗi
- `scripts/fixtures/`: Scripts tạo dữ liệu mẫu

## Cách sử dụng

### Chạy Tests

Các file test có thể được chạy trực tiếp từ trình duyệt hoặc qua PHP CLI.

Ví dụ:
```
php tests/booking/test_booking_simple.php
```

hoặc truy cập:
```
http://localhost/buffet_booking_mvc/tests/booking/test_booking_simple.php
```

### Chạy Scripts

Các script cũng có thể được chạy từ trình duyệt hoặc PHP CLI.

Ví dụ:
```
php scripts/database/setup_tables.php
```

hoặc truy cập:
```
http://localhost/buffet_booking_mvc/scripts/database/setup_tables.php
```

## Lưu ý quan trọng

- Hãy cẩn thận khi chạy các script trong thư mục `scripts/fixes/` và `scripts/database/` vì chúng có thể thay đổi cấu trúc và dữ liệu trong cơ sở dữ liệu
- Nên sao lưu dữ liệu trước khi chạy các script này
- Các script trong thư mục `scripts/fixtures/` chỉ nên sử dụng trong môi trường phát triển hoặc kiểm thử

## Thứ tự chạy khuyến nghị

1. `scripts/database/setup_tables.php` - Cài đặt cấu trúc cơ sở dữ liệu
2. `scripts/fixtures/add_sample_news.php` - Thêm dữ liệu mẫu cho tin tức
3. `scripts/fixtures/create_sample_orders.php` - Tạo đơn hàng mẫu
4. `tests/database/check_db_tables.php` - Kiểm tra cấu trúc cơ sở dữ liệu
5. Sau đó, có thể chạy các file test trong thư mục `tests/` tùy theo chức năng cần kiểm tra
