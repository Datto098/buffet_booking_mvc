# VNPay Integration Configuration Guide

## Overview
This guide explains how to configure VNPay payment gateway integration for the Buffet Booking MVC application.

## Configuration Steps

### 1. Database Migration
The database migration has been completed. The following tables were created:
- `payments` - Stores VNPay transaction data
- Updated `orders` table with VNPay payment method
- Created `v_payments_with_orders` view for admin management

### 2. VNPay Credentials Setup

Update the file `config/vnpay.php` with your VNPay credentials:

```php
// Replace with your actual VNPay credentials
define('VNP_TMNCODE', 'YOUR_ACTUAL_TMN_CODE'); // Terminal ID from VNPay
define('VNP_HASHSECRET', 'YOUR_ACTUAL_HASH_SECRET'); // Secret key from VNPay
```

**Test Credentials (Sandbox):**
- TMN Code: Use the test TMN code provided by VNPay
- Hash Secret: Use the test hash secret provided by VNPay
- URL: https://sandbox.vnpayment.vn/paymentv2/vpcpay.html

**Production Credentials:**
- TMN Code: Your production TMN code
- Hash Secret: Your production hash secret
- URL: https://pay.vnpay.vn/vpcpay.html

### 3. Test Payment Flow

To test the VNPay integration:

1. Add items to cart
2. Go to checkout page
3. Select "VNPay" payment method
4. Choose a bank (optional)
5. Click "Đặt Hàng Ngay"
6. You'll be redirected to VNPay payment page
7. Complete payment (use test cards in sandbox)
8. Return to order confirmation page

### 4. Admin Payment Management

Admin can manage payments at:
- URL: `/admin/payments`
- Features:
  - View all payments with filters
  - Payment details modal
  - Export payments to CSV
  - Cancel pending payments
  - Payment statistics

### 5. Test Card Numbers (Sandbox)

VNPay Sandbox test cards:
- Successful payment: Use any valid card format
- Failed payment: Use invalid card numbers
- Bank transfer: Use test bank accounts provided by VNPay

### 6. URL Endpoints

The following URLs are configured:
- Payment initiation: `/payment/confirm_vnpay`
- Payment return: `/payment/vnpay_return`
- IPN webhook: `/payment/vnpay_ipn`
- Admin payments: `/admin/payments`

### 7. Security Considerations

- VNPay secure hash validates all transactions
- Payment data is encrypted and stored securely
- Return URL and IPN provide double verification
- Admin access requires proper authentication

### 8. Troubleshooting

Common issues:
1. **Invalid TMN Code**: Check credentials in config/vnpay.php
2. **Hash Mismatch**: Verify hash secret is correct
3. **Payment Not Found**: Check database payment table
4. **Redirect Issues**: Verify return URL configuration

### 9. Production Deployment

Before going live:
1. Update VNPay credentials to production values
2. Change VNP_URL to production endpoint
3. Test with small amounts first
4. Monitor payment logs for issues
5. Set up proper backup procedures

### 10. Support

For VNPay integration support:
- VNPay Documentation: https://sandbox.vnpayment.vn/apis/
- VNPay Support: Contact VNPay technical team
- Application Issues: Check application logs

## Files Modified/Created

1. `controllers/PaymentController.php` - VNPay payment processing
2. `models/Payment.php` - Payment data model
3. `views/customer/payment/vnpay_payment.php` - Payment form
4. `views/admin/payments/index.php` - Admin payment management
5. `config/vnpay.php` - VNPay configuration
6. `database/migrations/create_vnpay_payment_tables.sql` - Database schema
7. Updated `controllers/OrderController.php` - VNPay order flow
8. Updated `controllers/AdminController.php` - Payment management
9. Updated `views/customer/order/checkout.php` - VNPay option
10. Updated `index.php` - Payment routing

## Integration Status

✅ Database schema created
✅ Payment model implemented
✅ Payment controller created
✅ Order integration completed
✅ Admin interface ready
✅ Frontend checkout updated
✅ Routing configured
⚠️ VNPay credentials need to be set
⚠️ Testing required

## Next Steps

1. Set up VNPay sandbox account
2. Update credentials in config/vnpay.php
3. Test payment flow
4. Train admin users on payment management
5. Monitor transactions and resolve issues
