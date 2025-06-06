# Orders Tests

Thư mục này chứa các file test liên quan đến chức năng đặt món và quản lý đơn hàng.

## Danh sách các file test:

- `check_order_items_schema.php`: Kiểm tra schema của bảng order_items
- `check_orders_schema.php`: Kiểm tra schema của bảng orders
- `test_order_details_fixed.php`: Test chi tiết đơn hàng sau khi sửa lỗi
- `test_order_details.php`: Test chi tiết đơn hàng
- `test_order_management_complete.php`: Test toàn bộ hệ thống quản lý đơn hàng
- `test_order_management.php`: Test chức năng quản lý đơn hàng
- `test_orders.php`: Test chung về đơn hàng
- `test_print_order.php`: Test chức năng in đơn hàng

## Cách sử dụng:

Các file test có thể được chạy trực tiếp từ trình duyệt hoặc qua PHP CLI.

Ví dụ:
```
php tests/orders/test_orders.php
```

hoặc truy cập:
```
http://localhost/buffet_booking_mvc/tests/orders/test_orders.php
```
