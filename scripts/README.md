# Scripts Guide

Tài liệu này mô tả các script hỗ trợ cho dự án Buffet Booking MVC.

## Cấu trúc thư mục

Thư mục `scripts` chứa các script thực hiện các tác vụ cụ thể, được chia thành các thư mục con:

- `database/`: Scripts liên quan đến cơ sở dữ liệu
- `fixes/`: Scripts sửa lỗi
- `fixtures/`: Scripts tạo dữ liệu mẫu

## Chi tiết từng loại script

### Database Scripts

Thư mục `database` chứa các script liên quan đến cơ sở dữ liệu:
- Tạo bảng và cấu trúc
- Kiểm tra kết nối
- Cài đặt schema

Xem chi tiết trong [README của database](./database/README.md).

### Fixes Scripts

Thư mục `fixes` chứa các script sửa lỗi cho hệ thống:
- Sửa lỗi tham chiếu cột
- Sửa lỗi cơ sở dữ liệu
- Cập nhật cấu trúc dữ liệu

Xem chi tiết trong [README của fixes](./fixes/README.md).

### Fixtures Scripts

Thư mục `fixtures` chứa các script tạo dữ liệu mẫu:
- Thêm dữ liệu tin tức mẫu
- Tạo đơn hàng mẫu
- Thêm dữ liệu người dùng mẫu

Xem chi tiết trong [README của fixtures](./fixtures/README.md).

## Quy trình sử dụng khuyến nghị

1. Cài đặt cơ sở dữ liệu với `database/setup_tables.php`
2. Chạy các script sửa lỗi nếu cần thiết
3. Thêm dữ liệu mẫu để kiểm thử bằng các script trong thư mục `fixtures`

## Lưu ý quan trọng

- Sao lưu dữ liệu trước khi chạy bất kỳ script nào
- Không chạy các script sửa lỗi nếu không có vấn đề cần sửa
- Chỉ sử dụng scripts fixture trong môi trường phát triển và kiểm thử
