# Buffet Booking & Restaurant Management System

A comprehensive PHP-based restaurant management system with buffet booking functionality, built using pure PHP and MySQL following the MVC (Model-View-Controller) pattern.

## Features

### Customer Features
- **User Registration & Authentication**: Secure login/register system
- **Menu Browsing**: View detailed food items with categories and subcategories
- **Shopping Cart**: Add items to cart, manage quantities
- **Order Management**: Place orders, track order history and status
- **Table Booking**: Reserve tables for buffet dining
- **User Profile**: Manage personal information and addresses
- **Promotions**: View and apply discount codes
- **News & Updates**: Stay updated with restaurant news

### Admin Features (Manager)
- **Food Management**: CRUD operations for food items
- **Category Management**: Manage food categories and subcategories
- **News Management**: Create and manage news articles
- **Profile Management**: Update admin profile
- **Statistics**: View basic statistics and reports

### Super Admin Features
- **User Management**: Manage all user accounts and roles
- **Order Management**: View and update order status
- **Booking Management**: Manage table reservations
- **Table Management**: CRUD operations for restaurant tables
- **Restaurant Info**: Update restaurant information
- **Promotion Management**: Create and manage discount codes
- **Review Management**: Moderate customer reviews
- **Advanced Statistics**: Comprehensive reports and analytics

## Technology Stack

- **Backend**: PHP 7.4+ (Pure PHP, no frameworks)
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Libraries**: jQuery, Font Awesome
- **Server**: Apache (XAMPP/WAMP compatible)

## Project Structure

```
buffet_booking_mvc/
├── config/
│   ├── config.php          # General configuration
│   └── database.php        # Database connection
├── controllers/
│   ├── BaseController.php  # Base controller class
│   ├── HomeController.php  # Home page controller
│   ├── AuthController.php  # Authentication controller
│   └── ...
├── models/
│   ├── BaseModel.php      # Base model class
│   ├── User.php           # User model
│   ├── Food.php           # Food model
│   ├── Order.php          # Order model
│   ├── Booking.php        # Booking model
│   └── ...
├── views/
│   ├── layouts/           # Header, footer templates
│   ├── customer/          # Customer-facing views
│   ├── admin/             # Manager dashboard views
│   └── superadmin/        # Super admin dashboard views
├── assets/
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── images/           # Static images
├── uploads/
│   ├── food_images/      # Food item images
│   ├── news_images/      # News article images
│   └── user_avatars/     # User profile pictures
├── admin/                # Admin dashboard entry point
├── superadmin/           # Super admin dashboard entry point
└── index.php             # Main entry point (Front Controller)
```

## Installation

1. **Clone or download** the project to your web server directory:
   ```
   c:\wamp64\www\buffet_booking_mvc\
   ```

2. **Create MySQL Database**:
   - Create a new database named `buffet_booking`
   - Import the SQL schema from `PLAN.vi.md` or create tables manually

3. **Configure Database Connection**:
   - Edit `config/database.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'buffet_booking');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

4. **Set Permissions**:
   - Ensure `uploads/` directory is writable
   - Set appropriate file permissions for image uploads

5. **Access the Application**:
   - Customer Interface: `http://localhost/buffet_booking_mvc/`
   - Manager Dashboard: `http://localhost/buffet_booking_mvc/admin/`
   - Super Admin Dashboard: `http://localhost/buffet_booking_mvc/superadmin/`

## Database Schema

The system uses the following main tables:
- `users` - Store all user accounts (customers, managers, admins)
- `categories` & `sub_categories` - Food categorization
- `food_items` - Menu items and buffet options
- `orders` & `order_items` - Order management
- `reservations` - Table booking system
- `tables` - Restaurant table information
- `promotions` - Discount codes and offers
- `reviews` - Customer reviews and ratings
- `news` - News articles and updates
- `user_addresses` - Customer delivery addresses
- `restaurant_info` - Restaurant information

## Key Features Implementation

### MVC Architecture
- **Models**: Handle database operations and business logic
- **Views**: Present data to users with responsive design
- **Controllers**: Process user input and coordinate between models and views

### Security Features
- CSRF token protection
- Password hashing using PHP's `password_hash()`
- Input sanitization and validation
- SQL injection prevention using prepared statements
- Session management with timeout

### Responsive Design
- Mobile-first approach with Bootstrap 5
- Optimized for desktop, tablet, and mobile devices
- Modern UI with smooth animations and transitions

### Cart Management
- Session-based shopping cart
- AJAX-powered cart updates
- Promotion code application
- Persistent cart across sessions

## Usage

### For Customers
1. Register an account or login
2. Browse the menu and add items to cart
3. Proceed to checkout and place orders
4. Book tables for buffet dining
5. Track order history and status

### For Managers
1. Login with manager credentials
2. Manage food items and categories
3. Create news articles
4. View basic statistics

### For Super Admins
1. Login with admin credentials
2. Manage all aspects of the system
3. View comprehensive reports
4. Manage user accounts and permissions

## Contributing

1. Follow PSR coding standards
2. Maintain the MVC structure
3. Add proper documentation for new features
4. Test thoroughly before submitting changes

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support and questions, please contact the development team or create an issue in the project repository.

---

**Note**: This is a learning project demonstrating PHP MVC architecture and restaurant management system implementation. For production use, consider additional security measures and performance optimizations.
