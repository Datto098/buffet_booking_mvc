# Review Management System - Super Admin

## Overview
The Review Management System allows Super Admins to manage customer reviews for food items. This includes viewing, approving/rejecting, verifying, and deleting reviews.

## Features

### 1. Review Dashboard
- **Statistics Cards**: Display total reviews, approved reviews, pending reviews, and average rating
- **Filter Options**: Filter by status (approved/pending/verified), rating (1-5 stars), and search
- **Bulk Actions**: Approve or delete multiple reviews at once

### 2. Review Management
- **View Details**: Complete review information including customer and food item details
- **Approval System**: Approve or reject reviews
- **Verification System**: Mark reviews as verified for additional credibility
- **Delete Reviews**: Remove inappropriate or spam reviews

### 3. Review Information
Each review displays:
- Customer information (name, email, avatar)
- Food item details (name, image, category, price)
- Rating (1-5 stars)
- Review title and comment
- Helpful vote count
- Status indicators (approved/pending, verified/unverified)
- Creation and update timestamps

## Access
- **URL**: `/superadmin/reviews`
- **Navigation**: Super Admin Dashboard → Reviews Management
- **Permission**: Super Admin role required

## Database Structure
The system uses the `reviews` table with the following columns:
- `id`: Primary key
- `user_id`: Customer who wrote the review
- `order_id`: Associated order (optional)
- `food_item_id`: Food item being reviewed
- `rating`: 1-5 star rating
- `title`: Review title (optional)
- `comment`: Review content
- `is_verified`: Verification status (0/1)
- `is_approved`: Approval status (0/1)
- `helpful_count`: Number of helpful votes
- `created_at`: Creation timestamp
- `updated_at`: Last modification timestamp

## API Endpoints
- `GET /superadmin/reviews` - Main review management page
- `GET /superadmin/reviews/details/{id}` - Get review details modal
- `POST /superadmin/reviews/approve/{id}` - Approve a review
- `POST /superadmin/reviews/reject/{id}` - Reject a review
- `POST /superadmin/reviews/verify/{id}` - Verify a review
- `POST /superadmin/reviews/delete/{id}` - Delete a review
- `POST /superadmin/reviews/bulk-action` - Bulk approve/delete reviews
- `GET /superadmin/reviews/stats` - Get review statistics

## Files Structure
```
controllers/SuperAdminController.php - Review management logic
models/Review.php - Review data operations
views/superadmin/reviews/
  ├── index.php - Main review management interface
  └── details.php - Review details modal
views/layouts/superadmin_sidebar.php - Navigation menu
index.php - Routing configuration
```

## Usage Instructions

### Viewing Reviews
1. Navigate to Super Admin → Reviews Management
2. Use filters to find specific reviews
3. Click the eye icon to view full review details

### Managing Reviews
1. **Approve**: Click the checkmark button or use bulk actions
2. **Reject**: Click the X button (for approved reviews)
3. **Verify**: Click the shield button (adds verification badge)
4. **Delete**: Click the trash button (permanent removal)

### Bulk Operations
1. Select multiple reviews using checkboxes
2. Use "Approve Selected" or "Delete Selected" buttons
3. Confirm the action in the popup dialog

## Security Features
- CSRF protection on all form submissions
- Super Admin role verification
- Input sanitization and validation
- SQL injection prevention through prepared statements

## Performance Considerations
- Pagination for large review datasets (20 reviews per page)
- Indexed database columns for efficient filtering
- AJAX-based operations for smooth user experience
- Optimized SQL queries with proper JOINs

## Maintenance
- Regular database cleanup of deleted reviews
- Monitor review approval queue
- Track review verification status
- Generate periodic review reports

## Integration
The review system integrates with:
- User management (customer information)
- Food item management (product details)
- Order management (purchase verification)
- Dashboard statistics

This system provides comprehensive review management capabilities while maintaining data integrity and user experience.
