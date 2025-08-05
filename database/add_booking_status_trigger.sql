-- Trigger to create notifications when booking status is updated
-- This will notify all super_admin users about booking status changes

DELIMITER $$

DROP TRIGGER IF EXISTS `booking_status_update_trigger`$$

CREATE TRIGGER `booking_status_update_trigger`
AFTER UPDATE ON `bookings`
FOR EACH ROW
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE admin_id INT;
    DECLARE notification_title VARCHAR(255);
    DECLARE notification_message TEXT;
    DECLARE notification_data JSON;
    DECLARE status_changed BOOLEAN DEFAULT FALSE;

    -- Cursor to get all super_admin users
    DECLARE admin_cursor CURSOR FOR
        SELECT id FROM users WHERE role = 'super_admin';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Check if status was changed
    IF OLD.status != NEW.status THEN
        SET status_changed = TRUE;
    END IF;

    -- Only create notification if status changed
    IF status_changed THEN
        -- Prepare notification content based on new status
        CASE NEW.status
            WHEN 'confirmed' THEN
                SET notification_title = 'Booking Confirmed';
                SET notification_message = CONCAT(
                    'Booking #', NEW.id, ' for ', NEW.customer_name,
                    ' has been confirmed for ', DATE_FORMAT(CONCAT(NEW.booking_date, ' ', NEW.booking_time), '%M %e, %Y at %l:%i %p')
                );
            WHEN 'cancelled' THEN
                SET notification_title = 'Booking Cancelled';
                SET notification_message = CONCAT(
                    'Booking #', NEW.id, ' for ', NEW.customer_name,
                    ' has been cancelled (was scheduled for ', DATE_FORMAT(CONCAT(NEW.booking_date, ' ', NEW.booking_time), '%M %e, %Y at %l:%i %p'), ')'
                );
            WHEN 'completed' THEN
                SET notification_title = 'Booking Completed';
                SET notification_message = CONCAT(
                    'Booking #', NEW.id, ' for ', NEW.customer_name,
                    ' has been completed successfully'
                );
            WHEN 'no_show' THEN
                SET notification_title = 'Booking No-Show';
                SET notification_message = CONCAT(
                    'Customer ', NEW.customer_name, ' did not show up for booking #', NEW.id,
                    ' scheduled for ', DATE_FORMAT(CONCAT(NEW.booking_date, ' ', NEW.booking_time), '%M %e, %Y at %l:%i %p')
                );
            WHEN 'seated' THEN
                SET notification_title = 'Customer Seated';
                SET notification_message = CONCAT(
                    'Customer ', NEW.customer_name, ' has been seated for booking #', NEW.id
                );
            ELSE
                SET notification_title = 'Booking Status Updated';
                SET notification_message = CONCAT(
                    'Booking #', NEW.id, ' for ', NEW.customer_name,
                    ' status changed from ', OLD.status, ' to ', NEW.status
                );
        END CASE;

        -- Prepare notification data as JSON
        SET notification_data = JSON_OBJECT(
            'booking_id', NEW.id,
            'customer_name', NEW.customer_name,
            'old_status', OLD.status,
            'new_status', NEW.status,
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
                'booking_status_update',
                notification_title,
                notification_message,
                notification_data,
                0,
                NOW()
            );

        END LOOP;

        CLOSE admin_cursor;

    END IF;

END$$

DELIMITER ;
