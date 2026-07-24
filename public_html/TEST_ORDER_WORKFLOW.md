# ORDER WORKFLOW TEST GUIDE

## Complete Order Placement Workflow - Fixed Issues

### Issues Fixed:
1. ✅ **create_order() function** - Added user details (full_name, email, phone) capture from users table
2. ✅ **Cart clearing** - Added automatic cart clearing after successful order creation
3. ✅ **User type validation** - Added customer type check in checkout.php, payment.php, create-order.php
4. ✅ **Error handling** - Improved error messages with URL parameters instead of sessions
5. ✅ **Payment page** - Added success/error message display

### Test Steps:

#### 1. User Registration/Login
- [ ] Go to `user-login.php`
- [ ] Login with customer credentials
- [ ] Verify session has `user_type = 'customer'`

#### 2. Add to Cart
- [ ] Browse books on `index.php`
- [ ] Click "Add to Cart" on a paid book (price > 0)
- [ ] Verify redirect to `cart.php` with success message
- [ ] Verify cart count badge updates
- [ ] Try adding same book again - should show "already in cart"
- [ ] Try adding free book - should show error

#### 3. View Cart
- [ ] Go to `cart.php`
- [ ] Verify all cart items display with:
  - Book cover image
  - Title, author, category
  - Price
  - Remove button
- [ ] Verify cart total calculation
- [ ] Test "Remove" button - item should be removed
- [ ] Test "Clear Cart" button - all items removed

#### 4. Checkout
- [ ] Add items to cart
- [ ] Click "Proceed to Checkout"
- [ ] Verify redirect to `checkout.php`
- [ ] Verify customer details display (name, email, phone)
- [ ] Verify order items list
- [ ] Verify order summary with total
- [ ] Check "I agree to terms" checkbox
- [ ] Verify "Proceed to Payment" button enables

#### 5. Create Order
- [ ] Click "Proceed to Payment"
- [ ] Verify redirect to `payment.php`
- [ ] Check database:
  ```sql
  SELECT * FROM orders ORDER BY id DESC LIMIT 1;
  -- Should have: user_id, order_number, full_name, email, phone, total_amount, status='pending'
  
  SELECT * FROM order_items WHERE order_id = [last_order_id];
  -- Should have all cart items with book_id and price
  
  SELECT * FROM cart WHERE user_id = [user_id];
  -- Should be empty (cart cleared)
  
  SELECT * FROM payments WHERE order_id = [last_order_id];
  -- Should have payment record with status='pending'
  ```

#### 6. Payment Page
- [ ] Verify order details display (order number, amount)
- [ ] Verify QR code generates
- [ ] Verify "Pay Now" UPI link works
- [ ] Enter transaction ID (12 digits)
- [ ] Enter UPI ID (optional)
- [ ] Click "Submit Transaction ID"
- [ ] Verify redirect to `payment-pending.php`
- [ ] Check database:
  ```sql
  SELECT * FROM payments WHERE order_id = [order_id];
  -- Should have: transaction_id, upi_id, status='pending'
  ```

#### 7. Admin Payment Verification
- [ ] Login as admin
- [ ] Go to "Verify Payments"
- [ ] Find pending payment
- [ ] Click "Verify"
- [ ] Check database:
  ```sql
  SELECT * FROM payments WHERE id = [payment_id];
  -- status should be 'completed'
  
  SELECT * FROM orders WHERE id = [order_id];
  -- status should be 'completed'
  ```

#### 8. Download Books
- [ ] Login as customer
- [ ] Go to "My Orders"
- [ ] Click "View Details" on completed order
- [ ] Click download button on each book
- [ ] Verify PDF downloads

### Database Validation Queries:

```sql
-- Check order with all details
SELECT o.*, u.full_name, u.email 
FROM orders o 
JOIN users u ON o.user_id = u.id 
ORDER BY o.id DESC LIMIT 1;

-- Check order items
SELECT oi.*, b.title, b.price 
FROM order_items oi 
JOIN books b ON oi.book_id = b.id 
WHERE oi.order_id = [order_id];

-- Check payment status
SELECT p.*, o.order_number 
FROM payments p 
JOIN orders o ON p.order_id = o.id 
WHERE p.order_id = [order_id];

-- Verify cart is empty after order
SELECT * FROM cart WHERE user_id = [user_id];
```

### Common Issues & Solutions:

#### Issue: "Add to Cart" not working
**Solution**: 
- Check user is logged in as customer
- Check `user_type = 'customer'` in session
- Check book price > 0
- Check book exists in database

#### Issue: Order creation fails
**Solution**:
- Check orders table has columns: user_id, order_number, full_name, email, phone, total_amount, status
- Check cart is not empty
- Check user exists in users table
- Check database connection

#### Issue: Payment page not loading
**Solution**:
- Check `order_id` in session
- Check order exists and belongs to user
- Check payment record created
- Check config/payment-config.php exists

#### Issue: Download not working
**Solution**:
- Check order status = 'completed'
- Check has_purchased() function
- Check file exists in uploads/files/
- Check file permissions

### Files Modified:
1. `php/func-order.php` - Fixed create_order() to capture user details and clear cart
2. `checkout.php` - Added user_type validation
3. `payment.php` - Added user_type validation and error/success messages
4. `php/create-order.php` - Added user_type validation, improved error handling
5. `php/submit-transaction.php` - Added user_type validation, improved error handling

### Expected Behavior:
✅ Customer can add paid books to cart
✅ Cart displays correctly with all items
✅ Checkout shows customer and order details
✅ Order creation captures user details automatically
✅ Cart clears after order creation
✅ Payment page displays with QR code and UPI link
✅ Transaction ID submission works
✅ Admin can verify payments
✅ Order status updates to completed
✅ Customer can download purchased books

### Test Complete! ✅
