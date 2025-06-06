# Booking System Documentation

## Overview

The booking system allows customers to reserve tables at the buffet restaurant. It checks for table availability based on party size and reservation time, and provides alternative time suggestions when the requested time is not available.

## Key Components

### 1. BookingController

The `BookingController` handles all booking-related actions:
- `index`: Displays the booking form
- `create`: Processes booking submissions
- `checkAvailability`: AJAX endpoint for checking table availability
- `myBookings`: Shows booking history for logged-in users
- `detail`: Displays booking details
- `cancel`: Handles booking cancellations

### 2. Booking Model

The `Booking` model manages the database operations:
- `getAvailableTables`: Finds available tables for a specific time and party size
- `checkAvailability`: Checks if tables are available for a booking
- `createBooking`: Creates a new booking record (with flexible field handling)
- `createBookingFromController`: Creates a booking from controller data with specific field mapping
- `getBookingsByUser`: Retrieves bookings for a specific user
- `updateBookingStatus`: Updates the status of a booking

## Field Mapping

The booking system handles field naming differences between the controller and model:
- Controller uses: `booking_datetime`, `party_size`, `special_requests`, `customer_phone`
- Model/Database uses: `reservation_time`, `number_of_guests`, `notes`, `phone_number`

This mapping is handled automatically in the `createBookingFromController` method and with flexible field handling in the `createBooking` method.

## Error Handling

The system includes robust error handling to ensure a smooth user experience:
- If the tables table doesn't exist or is empty, the system gracefully handles the error
- When booking times are unavailable, alternative time slots are suggested
- User-friendly error messages are displayed instead of technical errors

## Workflow

1. User selects date, time, and party size
2. System checks availability in real-time
3. If tables are available, user can proceed with the booking
4. If not available, alternative time slots are suggested
5. User submits booking details
6. System confirms the booking and sends confirmation
7. User can view, manage, or cancel bookings in their account

## Database Schema

### Tables Table
- `id`: Primary key
- `table_number`: Unique identifier for the table
- `capacity`: Maximum number of guests the table can accommodate
- `is_active`: Whether the table is available for booking

### Reservations Table
- `id`: Primary key
- `user_id`: Foreign key to the users table (for registered users)
- `customer_name`: Name of the customer
- `phone_number`: Contact number
- `table_id`: Foreign key to the tables table
- `reservation_time`: Date and time of the reservation
- `number_of_guests`: Number of guests
- `status`: Status of the booking (pending, confirmed, cancelled, completed)
- `notes`: Special requests or notes
- `created_at`: When the booking was created
- `updated_at`: When the booking was last updated

## JavaScript Integration

The booking form uses JavaScript to:
- Check availability in real-time
- Display available time slots
- Validate form inputs before submission
- Show toast notifications for user feedback

## Future Enhancements

1. Email and SMS notifications
2. Integration with a payment system for deposits
3. Table layout visualization
4. Wait list functionality for fully booked times
