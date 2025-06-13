# Sample Review Data Import Guide

## Overview
This guide helps you import realistic Vietnamese review data to test the review management system in the Super Admin panel.

## 📋 What's Included

### Review Data Features:
- **20 realistic Vietnamese reviews** for various food items
- **Mixed ratings**: 1-5 stars with authentic feedback
- **Various statuses**: Approved, pending, verified, and unverified reviews
- **Diverse food items**: Covers buffet items, individual dishes, drinks, and desserts
- **Authentic comments**: Real-looking Vietnamese customer feedback
- **Helpful vote counts**: Simulates community engagement

### Sample Review Categories:
1. **Buffet Reviews** (Deluxe, Standard, Vegetarian)
2. **Main Dishes** (Ba chỉ bò Mỹ, Sườn bò non, Mì udon, etc.)
3. **Japanese Items** (Sushi, Tempura, Gimbap)
4. **Drinks** (Trà đào cam sả, Trà tắc, Bia)
5. **Desserts** (Bánh flan)

## 🚀 Import Methods

### Method 1: Database Schema Import (Recommended)
The sample data is already included in the main database schema file:
```sql
c:\wamp64\www\buffet_booking_mvc\database\buffet_booking.sql
```

Simply reimport your database:
1. Drop existing `buffet_booking` database
2. Create new `buffet_booking` database
3. Import the updated SQL file
4. All sample reviews will be included automatically

### Method 2: Standalone SQL Script
```sql
c:\wamp64\www\buffet_booking_mvc\scripts\database\import_sample_reviews.sql
```

Run this script directly in phpMyAdmin or MySQL command line:
```bash
mysql -u root -p buffet_booking < import_sample_reviews.sql
```

### Method 3: PHP Import Script
```php
c:\wamp64\www\buffet_booking_mvc\scripts\database\import_sample_reviews.php
```

Run via web browser:
```
http://localhost/buffet_booking_mvc/scripts/database/import_sample_reviews.php
```

## 📊 Expected Results

After importing, you should have:
- **Total Reviews**: 20
- **Approved Reviews**: 17
- **Pending Reviews**: 3
- **Verified Reviews**: 12
- **Unverified Reviews**: 8
- **Average Rating**: ~4.1 stars

## 🧪 Testing Scenarios

### Basic Functionality:
1. **View All Reviews**: Navigate to Super Admin → Reviews Management
2. **Filter by Status**: Test approved/pending/verified filters
3. **Search Function**: Search by food names, customer names, or review content
4. **Rating Filter**: Filter by 1-5 star ratings

### Management Actions:
1. **Approve Reviews**: Click approve button on pending reviews
2. **Reject Reviews**: Click reject button on approved reviews
3. **Verify Reviews**: Add verification badges to reviews
4. **Delete Reviews**: Remove inappropriate content
5. **Bulk Operations**: Select multiple reviews and use bulk actions

### Advanced Features:
1. **Review Details**: Click eye icon to view complete review information
2. **Statistics**: Check dashboard statistics update correctly
3. **Pagination**: Test with different page sizes
4. **AJAX Operations**: Verify smooth UI interactions

## 🔍 Sample Review Data Details

### User Distribution:
- **User ID 1** (Adminn User): 6 reviews
- **User ID 5** (New User): 7 reviews
- **User ID 7** (hehe User): 7 reviews

### Food Item Coverage:
- **Buffet Items**: Deluxe Buffet, Standard Buffet, Vegetarian Special
- **Meat Dishes**: Ba chỉ bò Mỹ, Sườn non bò non
- **Japanese Food**: Sushi cá hồi, Gimbap Hàn, Tempura tôm, Há cảo hấp
- **Rice/Noodles**: Cơm chiên Nhật, Mì udon xào bò
- **Beverages**: Trà đào cam sả, Trà tắc, Bia
- **Desserts**: Bánh flan

### Review Quality Mix:
- **5-star reviews**: 8 reviews (excellent experiences)
- **4-star reviews**: 8 reviews (good with minor issues)
- **3-star reviews**: 3 reviews (average experiences)
- **2-star reviews**: 1 review (below expectations)

## 🛡️ Security Considerations

- All sample data uses existing user IDs from the users table
- Reviews reference valid food item IDs from food_items table
- No sensitive or inappropriate content included
- Proper character encoding for Vietnamese text
- Safe for production environments (can be easily cleared)

## 🔄 Cleanup (Optional)

To remove sample reviews later:
```sql
DELETE FROM reviews WHERE id BETWEEN 1 AND 20;
ALTER TABLE reviews AUTO_INCREMENT = 1;
```

## 🎯 Access Points

After importing data, access the review management system:
- **URL**: `http://localhost/buffet_booking_mvc/superadmin/reviews`
- **Login**: Use your Super Admin credentials
- **Navigation**: Super Admin Dashboard → Reviews Management

## 📞 Support

If you encounter any issues:
1. Check database connection settings
2. Verify user and food_items table data exists
3. Ensure proper foreign key relationships
4. Check for any SQL errors in the import process

The sample data is designed to provide a comprehensive testing environment for the review management system while maintaining data integrity and realistic user scenarios.
