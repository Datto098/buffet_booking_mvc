# Fixes Scripts

Thư mục này chứa các script sửa lỗi cho hệ thống.

## Danh sách các script:

- `fix_column_references.php`: Sửa các tham chiếu cột không đúng
- `fix_database_issues.php`: Sửa các vấn đề chung về cơ sở dữ liệu
- `fix_database_v2.php`: Phiên bản 2 của script sửa lỗi cơ sở dữ liệu
- `fix_database.php`: Sửa lỗi cơ bản của cơ sở dữ liệu

## Cách sử dụng:

Các script có thể được chạy trực tiếp từ trình duyệt hoặc qua PHP CLI.

Ví dụ:
```
php scripts/fixes/fix_database.php
```

hoặc truy cập:
```
http://localhost/buffet_booking_mvc/scripts/fixes/fix_database.php
```

## Lưu ý:

Hãy cẩn thận khi chạy các script này vì chúng có thể thay đổi cấu trúc và dữ liệu trong cơ sở dữ liệu. Nên sao lưu dữ liệu trước khi chạy.
