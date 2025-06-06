# Development Plan for a Buffet Ticket & Restaurant Ordering Website

This document provides a detailed plan for building the project using pure PHP and MySQL, including an analysis of the required user interfaces, a database schema design, and a step-by-step execution plan.

## 1. User Interface (UI) Page Analysis

Based on the requirements, we can divide the interface into three main areas: Customer Interface, Manager Dashboard, and Administrator Dashboard.

### 1.1. Customer-Facing Interface (Client-Side)

1.  **Homepage (`index.php`):**
    * Display promotional banners, featured dishes, and popular buffet tickets.
    * A brief introduction to the restaurant, latest news.
    * Search bar.
2.  **Login / Register Pages (`login.php`, `register.php`):**
    * Forms for customer login and registration.
    * "Forgot Password" functionality.
3.  **Customer Profile Page (`profile.php`):**
    * View and edit personal information (name, email, phone number).
    * Change password.
4.  **Address Management Page (`addresses.php`):**
    * Add, edit, delete delivery addresses.
    * Set a default address.
5.  **Menu/Food Page (`menu.php`):**
    * Display a list of all food items and buffet tickets.
    * Filter by category (appetizers, main courses, buffet, etc.).
6.  **Food Detail Page (`food_detail.php`):**
    * Show images, detailed description, price, and reviews for a specific item.
    * "Add to Cart" button.
7.  **Cart Page (`cart.php`):**
    * List selected items, quantities, and subtotal.
    * Update quantity or remove items from the cart.
    * Apply promotion codes.
8.  **Checkout Page (`checkout.php`):**
    * Select delivery address.
    * Choose a payment method (e.g., Cash on Delivery, Bank Transfer).
    * Display the final total and a "Place Order" button.
9.  **Order Confirmation Page (`order_success.php`):**
    * Show a success message with the order ID.
10. **Order History Page (`order_history.php`):**
    * List all past orders.
    * View order details and track status (Processing, Delivering, Completed).
11. **Table Reservation Page (`booking.php`):**
    * Form to fill in booking details (name, phone, number of guests, date/time).
12. **Promotions Page (`promotions.php`):**
    * Display a list of all active promotional campaigns.
13. **News Pages (`news.php`, `news_detail.php`):**
    * A list of news articles and a detail page for each.
14. **About Us Page (`about.php`):**
    * Detailed information about the restaurant.
15. **Search Results Page (`search.php`):**
    * Display results for food item searches.

### 1.2. Manager Dashboard Interface (/admin)

* *Requires a separate login for staff.*
1.  **Main Dashboard (`/admin/index.php`):**
    * Display quick statistics (e.g., number of dishes, categories, new posts).
2.  **Food Management (`/admin/food_management.php`):**
    * CRUD (Create, Read, Update, Delete) for food items.
3.  **Category Management (`/admin/category_management.php`):**
    * CRUD for food categories.
4.  **Sub-category Management (`/admin/subcategory_management.php`):**
    * CRUD for sub-categories.
5.  **News Management (`/admin/news_management.php`):**
    * CRUD for news and blog posts.
6.  **Profile Management (`/admin/profile.php`):**
    * Edit the manager's personal profile.
7.  **View Statistics (`/admin/statistics.php`):**
    * View reports and statistics as permitted.

### 1.3. Administrator Dashboard Interface (/superadmin)

* *Uses the same login portal as the Manager but with a higher role.*
1.  **Main Dashboard (`/superadmin/index.php`):**
    * High-level overview statistics (revenue, new orders, new users).
2.  **User Management (`/superadmin/user_management.php`):**
    * CRUD for all accounts (Customer, Manager, other Admins).
    * Assign roles and permissions.
3.  **Order Management (`/superadmin/order_management.php`):**
    * View order lists, and details.
    * Update order statuses.
4.  **Reservation Management (`/superadmin/booking_management.php`):**
    * View, confirm, or cancel table reservation requests.
5.  **Table Management (`/superadmin/table_management.php`):**
    * CRUD for restaurant tables (table number, capacity).
6.  **Restaurant Info Management (`/superadmin/restaurant_info.php`):**
    * Edit the restaurant's name, address, phone, etc.
7.  **Promotion Management (`/superadmin/promotion_management.php`):**
    * CRUD for promotional codes and campaigns.
8.  **Review Management (`/superadmin/review_management.php`):**
    * View, approve, or delete customer reviews.
9.  **Advanced Statistics (`/superadmin/statistics.php`):**
    * View detailed reports on revenue, best-selling items, etc.

---

## 2. Database Schema Design (MySQL)

```sql
-- Table to store all users (Customers, Managers, Admins)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(20),
  `role` ENUM('customer', 'manager', 'admin') NOT NULL DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table to store customer addresses
CREATE TABLE `user_addresses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `address_line` TEXT NOT NULL,
  `is_default` BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Food categories table (e.g., Main Course, Dessert, Buffet)
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT
);

-- Sub-categories table (e.g., Chicken, Beef under Main Course)
CREATE TABLE `sub_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
);

-- Food items and buffet tickets table
CREATE TABLE `food_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10, 2) NOT NULL,
  `image_url` VARCHAR(255),
  `category_id` INT NOT NULL,
  `sub_category_id` INT,
  `is_available` BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`),
  FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories`(`id`) ON DELETE SET NULL
);

-- Orders table
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `address_id` INT NOT NULL,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('pending', 'processing', 'delivering', 'completed', 'cancelled') DEFAULT 'pending',
  `payment_method` VARCHAR(50),
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`address_id`) REFERENCES `user_addresses`(`id`)
);

-- Order items detail table
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `food_item_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL, -- Price at the time of order
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`food_item_id`) REFERENCES `food_items`(`id`)
);

-- Restaurant tables
CREATE TABLE `tables` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `table_number` VARCHAR(50) NOT NULL,
  `capacity` INT NOT NULL,
  `status` ENUM('available', 'reserved') DEFAULT 'available'
);

-- Table reservations table
CREATE TABLE `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT, -- Can be null for guests without an account
  `customer_name` VARCHAR(100) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `table_id` INT,
  `reservation_time` DATETIME NOT NULL,
  `number_of_guests` INT NOT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  `notes` TEXT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`)
);

-- Promotions table
CREATE TABLE `promotions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) UNIQUE NOT NULL,
  `description` TEXT,
  `discount_type` ENUM('percentage', 'fixed_amount') NOT NULL,
  `discount_value` DECIMAL(10, 2) NOT NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `is_active` BOOLEAN DEFAULT TRUE
);

-- Customer reviews table
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `food_item_id` INT NOT NULL,
  `rating` TINYINT NOT NULL, -- 1 to 5 stars
  `comment` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`food_item_id`) REFERENCES `food_items`(`id`) ON DELETE CASCADE
);

-- News/blog table
CREATE TABLE `news` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `author_id` INT NOT NULL,
  `image_url` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`author_id`) REFERENCES `users`(`id`)
);

-- Restaurant info table (only needs one row)
CREATE TABLE `restaurant_info` (
  `id` INT PRIMARY KEY DEFAULT 1,
  `name` VARCHAR(255) NOT NULL,
  `address` TEXT NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `about_us_content` TEXT
);
