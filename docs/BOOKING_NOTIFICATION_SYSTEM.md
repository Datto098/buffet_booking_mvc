# Booking Notification System Implementation

## Overview
Hệ thống thông báo booking tự động đã được triển khai thành công để thông báo cho super admin mỗi khi có booking mới hoặc thay đổi trạng thái booking.

## Features Implemented

### 1. Database Triggers
- **booking_notification_trigger**: Tự động tạo notification khi có booking mới
- **booking_status_update_trigger**: Tự động tạo notification khi cập nhật trạng thái booking

### 2. Notification Types
- **new_booking**: Thông báo booking mới được tạo
- **booking_status_update**: Thông báo thay đổi trạng thái booking (confirmed, cancelled, completed, no_show, seated)

### 3. PHP Integration
- **Notification Model**: Thêm method `createBookingNotification()`
- **Controllers**: Tích hợp vào AdminController và BookingController
- **Views**: Cập nhật giao diện hiển thị notifications

## How It Works

### Automatic Notifications (Database Triggers)
1. **Khi tạo booking mới**: Database trigger tự động tạo notification cho tất cả super_admin users
2. **Khi cập nhật trạng thái**: Database trigger tự động tạo notification về thay đổi trạng thái

### Manual Notifications (PHP Code)
1. **AdminController**: Gọi `createBookingNotification()` sau khi tạo booking
2. **BookingController**: Gọi `createBookingNotification()` sau khi customer tạo booking

## Database Structure

### Notifications Table
```sql
notifications (
    id INT PRIMARY KEY,
    user_id INT (FK to users),
    type VARCHAR(50), -- 'new_booking', 'booking_status_update'
    title VARCHAR(255),
    message TEXT,
    data JSON, -- Contains booking details
    is_read TINYINT(1),
    created_at TIMESTAMP
)
```

### JSON Data Structure
```json
{
    "booking_id": 123,
    "customer_name": "Customer Name",
    "reservation_time": "2025-07-25 19:00:00",
    "guest_count": 4,
    "booking_reference": "ABC123",
    "url": "/admin/bookings?id=123"
}
```

## Files Modified/Created

### Models
- `models/Notification.php`: Added `createBookingNotification()` method

### Controllers
- `controllers/AdminController.php`: Added notification creation
- `controllers/BookingController.php`: Added notification creation

### Views
- `views/superadmin/notifications/index.php`: Updated to support new notification types

### Database
- `database/add_booking_notification_trigger.sql`: Trigger for new bookings
- `database/add_booking_status_trigger.sql`: Trigger for status updates
- `database/install_booking_trigger.php`: Installation script
- `database/install_status_trigger.php`: Installation script
- `database/test_notification_system.php`: Testing script
- `database/cleanup_test_data.php`: Cleanup script

## Usage

### For Super Admin
1. Navigate to: `http://localhost/buffet_booking_mvc/superadmin/notifications`
2. View all notifications with filtering options
3. Click "View Booking" to go directly to booking details
4. Mark notifications as read

### For Developers
```php
// Manual notification creation (if needed)
$notificationModel = new Notification();
$notificationModel->createBookingNotification($bookingId);
```

## Testing

Run the test script to verify everything works:
```bash
php database/test_notification_system.php
```

## Maintenance

### Clean up test data:
```bash
php database/cleanup_test_data.php
```

### Check database triggers:
```sql
SHOW TRIGGERS LIKE 'bookings';
```

## Notification Flow

1. **New Booking Created**:
   - User creates booking (customer form or admin form)
   - Database trigger fires automatically
   - Notification created for all super_admins
   - PHP code also creates notification (backup)

2. **Booking Status Updated**:
   - Admin updates booking status
   - Database trigger fires automatically
   - Notification created for all super_admins

3. **Super Admin Views**:
   - Login as super_admin
   - Navigate to notifications page
   - See all booking-related notifications
   - Click to view booking details
   - Mark as read when done

## Benefits

1. **Automatic**: No need to manually create notifications
2. **Real-time**: Notifications appear immediately
3. **Comprehensive**: Covers both new bookings and status changes
4. **User-friendly**: Easy to view and manage
5. **Reliable**: Database triggers ensure notifications are always created

## Future Enhancements

1. Email notifications for super admins
2. Push notifications
3. SMS notifications for urgent bookings
4. Notification templates customization
5. Role-based notification preferences
