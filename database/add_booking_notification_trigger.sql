-- Trigger to automatically create notifications when new bookings are created
-- This will notify all super_admin users about new bookings

DELIMITER $$

DROP TRIGGER IF EXISTS `booking_notification_trigger`$$

CREATE TRIGGER `booking_notification_trigger`
AFTER INSERT ON `bookings`
FOR EACH ROW
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE admin_id INT;
    DECLARE notification_title VARCHAR(255);
    DECLARE notification_message TEXT;
    DECLARE notification_data JSON;

    -- Cursor to get all super_admin users
    DECLARE admin_cursor CURSOR FOR
        SELECT id FROM users WHERE role = 'super_admin';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Prepare notification content
    SET notification_title = 'New Booking Created';
    SET notification_message = CONCAT(
        'A new booking has been created by ', NEW.customer_name,
        ' for ', DATE_FORMAT(CONCAT(NEW.booking_date, ' ', NEW.booking_time), '%M %e, %Y at %l:%i %p'),
        ' (', NEW.guest_count, ' guests)'
    );

    -- Prepare notification data as JSON
    SET notification_data = JSON_OBJECT(
        'booking_id', NEW.id,
        'customer_name', NEW.customer_name,
        'reservation_time', CONCAT(NEW.booking_date, ' ', NEW.booking_time),
        'guest_count', NEW.guest_count,
        'booking_reference', NEW.booking_reference,
        'url', CONCAT('/admin/bookings?id=', NEW.id)
    );

    -- Open cursor and loop through all super_admin users
    OPEN admin_cursor;

    read_loop: LOOP
        FETCH admin_cursor INTO admin_id;

        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Insert notification for each super_admin
        INSERT INTO notifications (
            user_id,
            type,
            title,
            message,
            data,
            is_read,
            created_at
        ) VALUES (
            admin_id,
            'new_booking',
            notification_title,
            notification_message,
            notification_data,
            0,
            NOW()
        );

    END LOOP;

    CLOSE admin_cursor;

END$$

DELIMITER ;

-- Test the trigger by inserting a sample booking (optional - comment out if not needed)
/*
INSERT INTO bookings (
    customer_name,
    customer_email,
    customer_phone,
    booking_date,
    booking_time,
    guest_count,
    booking_location,
    special_requests,
    status,
    booking_reference
) VALUES (
    'Test Customer Trigger',
    'test.trigger@example.com',
    '0123456789',
    '2025-07-25',
    '19:00:00',
    4,
    'Test Location',
    'Test trigger functionality',
    'pending',
    CONCAT('TRG', UPPER(SUBSTRING(MD5(RAND()), 1, 7)))
);
*/
