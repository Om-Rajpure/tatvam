# Book Publication & Payment System - Scope Documentation

## Project Information
**Project Name:** Online Book Store - Payment Integration Module  
**Version:** 2.0.0  
**Base Version:** 1.0.0  
**Document Date:** 2024  
**Author:** Development Team

---

## 1. EXECUTIVE SUMMARY

### 1.1 Purpose
Extend the existing Online Book Store application to enable users to purchase books through UPI payment gateway and download purchased books after successful payment.

### 1.2 Current System Overview
- Public users can browse, search, and view books
- Admin can manage books, authors, and categories
- Books are freely downloadable without payment

### 1.3 Proposed Enhancement
- Add pricing mechanism for books
- Integrate UPI payment gateway
- Implement purchase history tracking
- Restrict downloads to paid users only
- Enable users to access purchased books anytime

---

## 2. SCOPE OF WORK

### 2.1 In-Scope Features

#### 2.1.1 Database Modifications
- Add `price` field to books table
- Create `users` table for customer registration
- Create `orders` table for purchase tracking
- Create `payments` table for payment records
- Create `user_books` table for purchased books mapping

#### 2.1.2 User Management
- User registration system
- User login/authentication
- User profile management
- Password reset functionality

#### 2.1.3 Book Pricing
- Admin can set book prices
- Display prices on book cards
- Free books (price = 0) remain downloadable
- Paid books show "Buy Now" button

#### 2.1.4 Shopping Cart
- Add books to cart
- View cart items
- Update cart quantities
- Remove items from cart
- Calculate total amount

#### 2.1.5 Payment Integration
- UPI payment gateway integration
- Support multiple UPI apps (GPay, PhonePe, Paytm, etc.)
- Generate UPI payment links
- QR code generation for UPI payment
- Payment verification system
- Transaction ID tracking

#### 2.1.6 Order Management
- Create order on checkout
- Order status tracking (Pending, Completed, Failed, Refunded)
- Order history for users
- Order details view
- Invoice generation

#### 2.1.7 Download Management
- Verify purchase before download
- Download only purchased books
- Track download count
- Download history
- Re-download capability for purchased books

#### 2.1.8 Admin Enhancements
- View all orders
- View payment transactions
- Order status management
- Sales reports
- Revenue analytics
- User purchase history

### 2.2 Out-of-Scope Features
- Credit/Debit card payment
- Net banking integration
- International payment gateways
- Subscription models
- Book preview/sample pages
- Book reviews and ratings
- Wishlist functionality
- Email notifications (Phase 2)
- SMS notifications (Phase 2)

---

## 3. TECHNICAL SPECIFICATIONS

### 3.1 Database Schema Changes

#### 3.1.1 Modified Tables

**books** (Add new column)
```sql
ALTER TABLE books ADD COLUMN price DECIMAL(10,2) DEFAULT 0.00 AFTER description;
```

#### 3.1.2 New Tables

**users**
```sql
CREATE TABLE users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**orders**
```sql
CREATE TABLE orders (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**order_items**
```sql
CREATE TABLE order_items (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    order_id INT(11) NOT NULL,
    book_id INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);
```

**payments**
```sql
CREATE TABLE payments (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    order_id INT(11) NOT NULL,
    transaction_id VARCHAR(255) UNIQUE NOT NULL,
    upi_id VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'UPI',
    payment_response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
```

**cart**
```sql
CREATE TABLE cart (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    book_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, book_id)
);
```

**downloads**
```sql
CREATE TABLE downloads (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    book_id INT(11) NOT NULL,
    order_id INT(11) NOT NULL,
    download_count INT(11) DEFAULT 0,
    last_downloaded TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
```

### 3.2 UPI Payment Integration

#### 3.2.1 UPI Payment Flow
1. User adds books to cart
2. User proceeds to checkout
3. System generates order with unique order number
4. System creates UPI payment link with parameters:
   - UPI ID (merchant)
   - Amount
   - Transaction reference
   - Merchant name
5. Display UPI QR code and payment link
6. User completes payment via UPI app
7. Payment gateway webhook/callback updates payment status
8. System verifies payment
9. Update order status to completed
10. Grant download access

#### 3.2.2 UPI Payment URL Format
```
upi://pay?pa=MERCHANT_UPI_ID&pn=MERCHANT_NAME&am=AMOUNT&tn=ORDER_NUMBER&cu=INR
```

**Parameters:**
- `pa` - Payee Address (Merchant UPI ID)
- `pn` - Payee Name (Merchant Name)
- `am` - Amount
- `tn` - Transaction Note (Order Number)
- `cu` - Currency (INR)

#### 3.2.3 Payment Verification Methods
- **Manual Verification**: Admin verifies payment and updates status
- **Webhook Integration**: Payment gateway sends callback on payment completion
- **Transaction ID Matching**: User enters transaction ID for verification

---

## 4. FUNCTIONAL REQUIREMENTS

### 4.1 User Registration & Authentication

#### FR-1.1: User Registration
- **Input**: Full Name, Email, Phone, Password, Confirm Password
- **Validation**: 
  - Email format validation
  - Phone number validation (10 digits)
  - Password minimum 6 characters
  - Password match confirmation
  - Unique email check
- **Output**: User account created, redirect to login

#### FR-1.2: User Login
- **Input**: Email, Password
- **Validation**: Credentials verification
- **Output**: Session created, redirect to homepage

#### FR-1.3: User Logout
- **Action**: Destroy user session
- **Output**: Redirect to homepage

### 4.2 Book Pricing Management

#### FR-2.1: Add/Edit Book Price (Admin)
- **Input**: Book price (decimal)
- **Validation**: Non-negative number
- **Output**: Price saved to database

#### FR-2.2: Display Book Price (Public)
- **Display**: 
  - Free books: "Free Download" button
  - Paid books: "₹ Price" and "Buy Now" button
  - Already purchased: "Download" button

### 4.3 Shopping Cart

#### FR-3.1: Add to Cart
- **Prerequisite**: User must be logged in
- **Input**: Book ID
- **Validation**: 
  - Book not already purchased
  - Book not already in cart
  - Book price > 0
- **Output**: Book added to cart, success message

#### FR-3.2: View Cart
- **Display**: 
  - List of cart items
  - Book details (title, author, price)
  - Total amount
  - Proceed to checkout button

#### FR-3.3: Remove from Cart
- **Input**: Cart item ID
- **Output**: Item removed, cart updated

#### FR-3.4: Clear Cart
- **Action**: Remove all items from user's cart
- **Output**: Empty cart message

### 4.4 Checkout & Payment

#### FR-4.1: Checkout Process
- **Prerequisite**: Cart not empty
- **Process**:
  1. Display order summary
  2. Show total amount
  3. Generate unique order number
  4. Create order record (status: pending)
  5. Create order items
  6. Redirect to payment page

#### FR-4.2: UPI Payment
- **Display**:
  - Order details
  - Total amount
  - UPI QR code
  - UPI payment link
  - Manual UPI ID input option
  - Transaction ID input field
- **Actions**:
  - Generate UPI deep link
  - Generate QR code
  - Open UPI apps on mobile
  - Submit transaction ID for verification

#### FR-4.3: Payment Verification
- **Manual Method**:
  - User enters transaction ID
  - Admin verifies payment
  - Admin updates order status
- **Automatic Method** (Future):
  - Webhook receives payment status
  - System auto-updates order status

#### FR-4.4: Payment Success
- **Actions**:
  - Update order status to 'completed'
  - Update payment status to 'success'
  - Create download access records
  - Clear user cart
  - Display success message with download links

#### FR-4.5: Payment Failure
- **Actions**:
  - Update order status to 'failed'
  - Update payment status to 'failed'
  - Display error message
  - Option to retry payment

### 4.5 Download Management

#### FR-5.1: Download Purchased Book
- **Prerequisite**: User logged in and book purchased
- **Validation**: 
  - Verify purchase record
  - Check order status = 'completed'
- **Action**: 
  - Increment download count
  - Update last downloaded timestamp
  - Serve file for download

#### FR-5.2: My Library
- **Display**: 
  - List of all purchased books
  - Download button for each book
  - Purchase date
  - Order number
  - Download count

#### FR-5.3: Download Restrictions
- **Free Books**: Anyone can download
- **Paid Books**: Only purchasers can download
- **Unpurchased Books**: Show "Buy Now" button

### 4.6 Order Management

#### FR-6.1: My Orders (User)
- **Display**:
  - Order number
  - Order date
  - Total amount
  - Status
  - Books in order
  - View details button

#### FR-6.2: Order Details (User)
- **Display**:
  - Complete order information
  - Payment details
  - Transaction ID
  - Books list with download buttons
  - Invoice download option

#### FR-6.3: All Orders (Admin)
- **Display**:
  - All orders from all users
  - Filter by status
  - Search by order number
  - User details
  - Payment status
  - Action buttons (verify, refund)

#### FR-6.4: Order Status Update (Admin)
- **Input**: Order ID, New Status
- **Actions**: Update order status
- **Trigger**: Grant/revoke download access

### 4.7 Admin Reports

#### FR-7.1: Sales Report
- **Display**:
  - Total revenue
  - Total orders
  - Total books sold
  - Date range filter
  - Export to CSV

#### FR-7.2: Payment Transactions
- **Display**:
  - All payment records
  - Transaction ID
  - Amount
  - Status
  - Date
  - User details

---

## 5. USER INTERFACE SPECIFICATIONS

### 5.1 New Pages Required

#### 5.1.1 User Pages
1. **register.php** - User registration form
2. **user-login.php** - User login form
3. **user-profile.php** - User profile management
4. **cart.php** - Shopping cart view
5. **checkout.php** - Order summary and checkout
6. **payment.php** - UPI payment interface
7. **payment-success.php** - Payment confirmation page
8. **payment-failed.php** - Payment failure page
9. **my-library.php** - Purchased books library
10. **my-orders.php** - Order history
11. **order-details.php** - Single order view

#### 5.1.2 Admin Pages
1. **admin-orders.php** - All orders management
2. **admin-payments.php** - Payment transactions
3. **admin-reports.php** - Sales and revenue reports
4. **verify-payment.php** - Manual payment verification

### 5.2 Modified Pages

#### 5.2.1 index.php
- Add price display on book cards
- Change download button to "Buy Now" for paid books
- Show "Download" for purchased books (logged-in users)
- Add "Add to Cart" button

#### 5.2.2 admin.php
- Add price column in books table
- Add link to orders management
- Add link to payment transactions
- Add link to reports

#### 5.2.3 add-book.php & edit-book.php
- Add price input field
- Validate price input

#### 5.2.4 Navigation
- Add "Cart" icon with item count
- Add "My Library" link for logged-in users
- Add "My Orders" link for logged-in users
- Add "Login/Register" or "Profile/Logout" based on session

---

## 6. FILE STRUCTURE

### 6.1 New PHP Files

```
bookstore/
├── register.php
├── user-login.php
├── user-profile.php
├── cart.php
├── checkout.php
├── payment.php
├── payment-success.php
├── payment-failed.php
├── my-library.php
├── my-orders.php
├── order-details.php
├── admin-orders.php
├── admin-payments.php
├── admin-reports.php
├── verify-payment.php
├── download.php (secure download handler)
│
├── php/
│   ├── user-register.php
│   ├── user-auth.php
│   ├── user-logout.php
│   ├── add-to-cart.php
│   ├── remove-from-cart.php
│   ├── clear-cart.php
│   ├── create-order.php
│   ├── process-payment.php
│   ├── verify-payment.php
│   ├── update-order-status.php
│   ├── func-user.php
│   ├── func-cart.php
│   ├── func-order.php
│   ├── func-payment.php
│   ├── func-download.php
│   └── generate-qr.php
│
└── css/
    └── payment.css (payment page styling)
```

---

## 7. SECURITY REQUIREMENTS

### 7.1 Authentication & Authorization
- Session-based authentication for users
- Separate admin and user sessions
- Password hashing using password_hash()
- CSRF token protection on forms
- Session timeout after inactivity

### 7.2 Payment Security
- Validate all payment amounts server-side
- Verify transaction IDs before granting access
- Log all payment attempts
- Prevent duplicate transaction processing
- Secure storage of transaction data

### 7.3 Download Security
- Verify user authentication before download
- Verify purchase before serving file
- Use secure download script (not direct file links)
- Log all download attempts
- Prevent unauthorized file access

### 7.4 Data Protection
- SQL injection prevention (prepared statements)
- XSS prevention (htmlspecialchars)
- Input validation and sanitization
- Secure file upload handling
- HTTPS enforcement (recommended)

---

## 8. TESTING REQUIREMENTS

### 8.1 Unit Testing
- User registration validation
- Login authentication
- Cart operations
- Order creation
- Payment processing
- Download verification

### 8.2 Integration Testing
- Complete purchase flow
- Payment gateway integration
- Order and payment status sync
- Download access after payment

### 8.3 User Acceptance Testing
- User registration and login
- Browse and add books to cart
- Complete checkout process
- Make UPI payment
- Download purchased books
- View order history

### 8.4 Admin Testing
- Manage book prices
- View and verify orders
- Update payment status
- Generate reports
- Verify payment manually

---

## 9. IMPLEMENTATION PHASES

### Phase 1: Database & User Management (Week 1)
- Create new database tables
- Implement user registration
- Implement user login/logout
- User profile management

### Phase 2: Pricing & Cart (Week 2)
- Add price field to books
- Update admin book management
- Implement shopping cart
- Cart operations (add, remove, view)

### Phase 3: Order & Checkout (Week 3)
- Order creation system
- Checkout process
- Order management pages
- Order history

### Phase 4: Payment Integration (Week 4)
- UPI payment link generation
- QR code generation
- Payment page UI
- Transaction ID submission

### Phase 5: Payment Verification (Week 5)
- Manual payment verification (Admin)
- Payment status updates
- Order status management
- Download access granting

### Phase 6: Download Management (Week 6)
- Secure download handler
- Purchase verification
- My Library page
- Download tracking

### Phase 7: Admin Features (Week 7)
- Orders management
- Payment transactions view
- Sales reports
- Revenue analytics

### Phase 8: Testing & Deployment (Week 8)
- Complete system testing
- Bug fixes
- Performance optimization
- Documentation
- Deployment

---

## 10. ASSUMPTIONS & CONSTRAINTS

### 10.1 Assumptions
- XAMPP/LAMP server environment
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Users have UPI-enabled apps
- Merchant has valid UPI ID
- Manual payment verification initially
- Internet connectivity required

### 10.2 Constraints
- No automated payment gateway API (manual verification)
- No email/SMS notifications in Phase 1
- No refund automation (manual process)
- No payment gateway fees calculation
- Single currency support (INR only)
- No installment/EMI options

### 10.3 Dependencies
- UPI payment apps (GPay, PhonePe, Paytm, etc.)
- QR code generation library (PHP QR Code)
- Bootstrap 5 for UI
- Existing bookstore codebase

---

## 11. RISKS & MITIGATION

### 11.1 Technical Risks

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Payment verification delays | High | High | Implement clear instructions, admin dashboard for quick verification |
| Fraudulent transactions | High | Medium | Transaction ID verification, order review system |
| Download link sharing | Medium | High | Secure download handler, session verification |
| Database performance | Medium | Low | Indexing, query optimization |
| Session hijacking | High | Low | Secure session handling, HTTPS |

### 11.2 Business Risks

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| User abandonment at payment | High | Medium | Clear instructions, multiple payment options |
| Payment disputes | Medium | Medium | Clear refund policy, transaction records |
| Low adoption rate | Medium | Low | User-friendly interface, free books option |

---

## 12. SUCCESS CRITERIA

### 12.1 Functional Success
- ✅ Users can register and login
- ✅ Users can add books to cart
- ✅ Users can complete checkout
- ✅ UPI payment link generated correctly
- ✅ Payment verification working
- ✅ Download access granted after payment
- ✅ Admin can manage orders and payments

### 12.2 Performance Success
- Page load time < 3 seconds
- Payment processing < 5 minutes (manual verification)
- Download initiation < 2 seconds
- Support 100+ concurrent users

### 12.3 User Experience Success
- Intuitive navigation
- Clear payment instructions
- Responsive design (mobile-friendly)
- Error messages are helpful
- Success confirmations are clear

---

## 13. FUTURE ENHANCEMENTS (Phase 2)

### 13.1 Automated Payment Gateway
- Integrate Razorpay/PayU/Instamojo
- Automatic payment verification
- Webhook implementation
- Real-time status updates

### 13.2 Notifications
- Email notifications (order confirmation, payment success)
- SMS notifications
- Push notifications

### 13.3 Advanced Features
- Book reviews and ratings
- Wishlist functionality
- Discount coupons
- Bulk purchase discounts
- Subscription plans
- Book preview/sample pages
- Reading progress tracking
- Multiple payment methods (cards, wallets)

### 13.4 Analytics
- User behavior tracking
- Sales analytics dashboard
- Popular books report
- Revenue forecasting
- Customer segmentation

### 13.5 Mobile App
- Android app
- iOS app
- Progressive Web App (PWA)

---

## 14. SUPPORT & MAINTENANCE

### 14.1 Documentation
- User manual
- Admin manual
- API documentation
- Database schema documentation
- Deployment guide

### 14.2 Training
- Admin training for order management
- Admin training for payment verification
- User guide for purchase process

### 14.3 Maintenance
- Regular database backups
- Security updates
- Bug fixes
- Performance monitoring
- Log analysis

---

## 15. DELIVERABLES

### 15.1 Code Deliverables
- Complete source code
- Database migration scripts
- Configuration files
- Installation guide

### 15.2 Documentation Deliverables
- Technical documentation
- User manual
- Admin manual
- API documentation
- Test cases and results

### 15.3 Database Deliverables
- Database schema
- Sample data
- Backup scripts

---

## 16. ACCEPTANCE CRITERIA

### 16.1 User Flow Acceptance
1. User can register successfully
2. User can login with credentials
3. User can browse books with prices
4. User can add paid books to cart
5. User can view cart and total amount
6. User can proceed to checkout
7. User can see UPI payment details
8. User can complete UPI payment
9. User can submit transaction ID
10. Admin can verify payment
11. User receives download access
12. User can download purchased books
13. User can view order history
14. User can re-download purchased books

### 16.2 Admin Flow Acceptance
1. Admin can set book prices
2. Admin can view all orders
3. Admin can view payment transactions
4. Admin can verify payments manually
5. Admin can update order status
6. Admin can view sales reports
7. Admin can search orders
8. Admin can filter by status

---

## 17. GLOSSARY

| Term | Definition |
|------|------------|
| UPI | Unified Payments Interface - Indian instant payment system |
| QR Code | Quick Response Code for payment scanning |
| Transaction ID | Unique identifier for payment transaction |
| Order Number | Unique identifier for customer order |
| Cart | Temporary storage for books before purchase |
| Checkout | Process of finalizing purchase |
| My Library | User's collection of purchased books |
| Payment Gateway | Service that processes online payments |
| Webhook | Automated callback for payment status |
| Session | User's authenticated state on website |

---

## 18. CONTACT & APPROVAL

### 18.1 Project Stakeholders
- **Project Manager**: [Name]
- **Lead Developer**: [Name]
- **Database Administrator**: [Name]
- **QA Lead**: [Name]
- **Business Owner**: [Name]

### 18.2 Approval Sign-off

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Business Owner | | | |
| Project Manager | | | |
| Lead Developer | | | |
| QA Lead | | | |

---

**Document Version:** 1.0  
**Last Updated:** 2024  
**Status:** Draft / Under Review / Approved  

---

## APPENDIX A: UPI Payment Example

### Sample UPI Payment URL
```
upi://pay?pa=merchant@upi&pn=BookStore&am=299.00&tn=ORD20240001&cu=INR
```

### Sample QR Code Data
```
upi://pay?pa=merchant@upi&pn=BookStore&am=299.00&tn=ORD20240001&cu=INR
```

---

## APPENDIX B: Database ER Diagram

```
users (1) ----< (M) orders
orders (1) ----< (M) order_items
order_items (M) >---- (1) books
orders (1) ----< (M) payments
users (1) ----< (M) cart
cart (M) >---- (1) books
users (1) ----< (M) downloads
downloads (M) >---- (1) books
downloads (M) >---- (1) orders
```

---

## APPENDIX C: Sample Order Flow Diagram

```
User Browse Books
    ↓
Add to Cart
    ↓
View Cart
    ↓
Proceed to Checkout
    ↓
Create Order (Pending)
    ↓
Generate UPI Payment Link
    ↓
Display QR Code
    ↓
User Pays via UPI App
    ↓
User Submits Transaction ID
    ↓
Admin Verifies Payment
    ↓
Update Order Status (Completed)
    ↓
Grant Download Access
    ↓
User Downloads Books
```

---

**END OF DOCUMENT**
