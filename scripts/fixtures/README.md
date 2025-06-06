# Fixtures Scripts

Thư mục này chứa các script tạo dữ liệu mẫu (fixtures) cho hệ thống.

## Danh sách các script:

- `add_sample_news.php`: Thêm dữ liệu mẫu cho bảng tin tức
- `create_sample_orders.php`: Tạo các đơn hàng mẫu

## Cách sử dụng:

Các script có thể được chạy trực tiếp từ trình duyệt hoặc qua PHP CLI.

Ví dụ:
```
php scripts/fixtures/add_sample_news.php
```

hoặc truy cập:
```
http://localhost/buffet_booking_mvc/scripts/fixtures/add_sample_news.php
```

## Lưu ý:

Các script này sẽ thêm dữ liệu mẫu vào cơ sở dữ liệu. Nên sử dụng chúng trong môi trường phát triển hoặc kiểm thử, không nên sử dụng trong môi trường sản xuất trừ khi cần thiết.
