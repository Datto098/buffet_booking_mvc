# 📧 Hướng Dẫn Sử Dụng Hệ Thống Thông Báo Nội Bộ

## 🎯 Tổng Quan

Hệ thống thông báo nội bộ cho phép **Super Admin** gửi thông báo realtime đến các **Admin** trong hệ thống quản lý buffet booking. Hệ thống hỗ trợ gửi thông báo cho nhiều người nhận, gửi broadcast, đính kèm file và phân loại theo mức độ ưu tiên.

## 🚀 Cách Truy Cập

### 🔴 Super Admin - Gửi Thông Báo

1. **Đăng nhập vào Super Admin Panel**
   - URL: `http://localhost/buffet_booking_mvc/superadmin`
   - Sử dụng tài khoản Super Admin

2. **Truy cập menu Internal Messages**
   - Click vào menu **"Internal Messages"** trong sidebar bên trái
   - Hoặc truy cập trực tiếp: `http://localhost/buffet_booking_mvc/superadmin/internal-messages`

3. **Gửi thông báo mới**
   - Click nút **"Gửi thông báo mới"** hoặc **"Soạn thông báo"**
   - Điền thông tin:
     - **Tiêu đề**: Tiêu đề thông báo (bắt buộc)
     - **Loại thông báo**: Chọn loại phù hợp
     - **Mức độ ưu tiên**: Thấp/Bình thường/Cao/Khẩn cấp
     - **Nội dung**: Nội dung chi tiết (bắt buộc)
     - **File đính kèm**: Tùy chọn (PDF, DOC, DOCX, TXT, JPG, PNG)
     - **Người nhận**: Chọn admin cụ thể hoặc "Gửi cho tất cả Admin"

4. **Xem thông báo đã gửi**
   - Click **"Thông báo đã gửi"** để xem danh sách
   - Xem chi tiết, thống kê đọc, xóa thông báo

### 🔵 Admin - Nhận Thông Báo

1. **Đăng nhập vào Admin Panel**
   - URL: `http://localhost/buffet_booking_mvc/admin`
   - Sử dụng tài khoản Admin

2. **Truy cập Internal Messages**
   - Click vào menu **"Internal Messages"** trong sidebar
   - Badge đỏ hiển thị số thông báo chưa đọc
   - Hoặc truy cập: `http://localhost/buffet_booking_mvc/admin/internal-messages`

3. **Xem thông báo**
   - Danh sách thông báo đã nhận (mới nhất trước)
   - Click vào thông báo để xem chi tiết
   - Thông báo tự động đánh dấu đã đọc khi xem

## 📋 Các Loại Thông Báo

| Loại | Mô tả | Badge |
|------|-------|-------|
| **Thông báo chung** | Thông tin chung, cập nhật | 🔵 |
| **Cập nhật hệ thống** | Thay đổi tính năng, bản cập nhật | 🔵 |
| **Thay đổi chính sách** | Quy định mới, chính sách | 🟡 |
| **Bảo trì** | Bảo trì hệ thống, downtime | 🔴 |
| **Thông báo cá nhân** | Thông tin riêng tư | 🟢 |

## ⚡ Mức Độ Ưu Tiên

| Mức độ | Mô tả | Màu sắc |
|--------|-------|---------|
| **Thấp** | Thông tin tham khảo | Xám |
| **Bình thường** | Thông báo thường | Xanh |
| **Cao** | Cần chú ý | Cam |
| **Khẩn cấp** | Cần xử lý ngay | Đỏ |

## 📎 Tính Năng File Đính Kèm

- **Định dạng hỗ trợ**: PDF, DOC, DOCX, TXT, JPG, PNG
- **Kích thước tối đa**: 5MB
- **Lưu trữ**: `uploads/internal_messages/`
- **Tải xuống**: Click vào tên file trong chi tiết thông báo

## 🔧 Tính Năng Nâng Cao

### Gửi Broadcast
- Chọn **"Gửi cho tất cả Admin"** để gửi đến tất cả admin
- Thông báo sẽ được gửi đến tất cả user có role `manager` hoặc `super_admin`

### Thống Kê
- **Super Admin**: Xem số người nhận, số người đã đọc
- **Admin**: Xem thời gian đã đọc thông báo

### Quản Lý
- **Xóa thông báo**: Chỉ người gửi mới có thể xóa
- **Lịch sử**: Lưu trữ đầy đủ lịch sử gửi/nhận

## 🛠️ Cài Đặt & Setup

### 1. Chạy Migration
```bash
php scripts/setup_internal_messages.php
```

### 2. Kiểm tra Database
- Bảng `internal_messages`: Lưu thông báo
- Bảng `internal_message_recipients`: Lưu người nhận và trạng thái đọc

### 3. Test Chức Năng
```bash
php tests/internal_messages/test_internal_messages.php
```

## 📁 Cấu Trúc File

```
models/
├── InternalMessage.php          # Model xử lý database
controllers/
├── InternalMessageController.php # Controller chính
├── SuperAdminController.php     # Controller Super Admin
├── AdminController.php          # Controller Admin
views/
├── superadmin/internal_messages/
│   ├── send.php                 # Gửi thông báo
│   ├── sent.php                 # Danh sách đã gửi
│   └── view.php                 # Xem chi tiết
├── admin/internal_messages/
│   ├── received.php             # Danh sách nhận
│   └── view.php                 # Xem chi tiết
uploads/
└── internal_messages/           # Thư mục lưu file
```

## 🔒 Bảo Mật

- **Xác thực**: Chỉ user đã đăng nhập mới truy cập được
- **Phân quyền**: Super Admin gửi, Admin nhận
- **CSRF Protection**: Token bảo vệ form
- **File Upload**: Kiểm tra định dạng và kích thước
- **SQL Injection**: Sử dụng Prepared Statements

## 🐛 Troubleshooting

### Lỗi thường gặp:

1. **Không thấy menu Internal Messages**
   - Kiểm tra role user (phải là super_admin hoặc manager)
   - Refresh cache browser

2. **Không gửi được thông báo**
   - Kiểm tra kết nối database
   - Kiểm tra quyền ghi thư mục uploads

3. **File không upload được**
   - Kiểm tra kích thước file (tối đa 5MB)
   - Kiểm tra định dạng file được hỗ trợ

4. **Thông báo không hiển thị**
   - Kiểm tra bảng database đã được tạo
   - Chạy lại script setup

## 📞 Hỗ Trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra log lỗi trong `logs/`
2. Chạy script test để debug
3. Liên hệ developer để hỗ trợ

---

**🎉 Chúc bạn sử dụng hệ thống thông báo nội bộ hiệu quả!**
