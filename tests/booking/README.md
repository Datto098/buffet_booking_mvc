# Booking System Tests

Thư mục này chứa các file test cho hệ thống đặt bàn (booking system).

## Danh sách các file test:

- `test_booking_debug.php`: Kiểm tra chức năng tạo booking để debug
- `test_booking_e2e.php`: Test end-to-end quy trình đặt bàn
- `test_booking_simple.php`: Test đơn giản chức năng đặt bàn
- `test_booking_system.php`: Test toàn bộ hệ thống đặt bàn
- `test_booking_tables.php`: Test bảng và việc quản lý bàn

## Cách sử dụng:

Các file test có thể được chạy trực tiếp từ trình duyệt hoặc qua PHP CLI.

Ví dụ:
```
php tests/booking/test_booking_simple.php
```

hoặc truy cập:
```
http://localhost/buffet_booking_mvc/tests/booking/test_booking_simple.php
```
