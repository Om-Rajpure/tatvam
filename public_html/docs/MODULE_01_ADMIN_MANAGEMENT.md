# MODULE 01: ADMIN MANAGEMENT SYSTEM

**Status:** ✅ COMPLETED (Existing)  
**Version:** 1.0.0  
**Dependencies:** None

---

## 1. MODULE OVERVIEW

### 1.1 Purpose
Admin authentication and management system for bookstore operations.

### 1.2 Current Implementation Status
- ✅ Admin login/logout
- ✅ Session management
- ✅ Dashboard view
- ✅ Book management
- ✅ Author management
- ✅ Category management

---

## 2. DATABASE SCHEMA

### 2.1 Existing Tables

**admin**
```sql
CREATE TABLE admin (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password TEXT NOT NULL
);
```

**Default Admin Credentials:**
- Email: eliasfsdev@gmail.com
- Password: 12345 (Hashed)

---

## 3. EXISTING FILES

### 3.1 Frontend Files
- `login.php` - Admin login page
- `admin.php` - Admin dashboard
- `logout.php` - Logout handler

### 3.2 Backend Files
- `php/auth.php` - Authentication handler
- `db_conn.php` - Database connection

### 3.3 Helper Functions
- None specific (uses inline code)

---

## 4. FUNCTIONAL SPECIFICATIONS

### 4.1 Admin Login
**File:** `login.php`
- **Input:** Email, Password
- **Validation:** 
  - Email format check
  - Password verification using password_verify()
- **Session Variables:**
  - `$_SESSION['user_id']`
  - `$_SESSION['user_email']`
- **Redirect:** admin.php on success

### 4.2 Admin Dashboard
**File:** `admin.php`
- **Access Control:** Session check required
- **Display:**
  - All books table
  - All categories table
  - All authors table
- **Actions:**
  - Edit/Delete books
  - Edit/Delete categories
  - Edit/Delete authors

### 4.3 Admin Logout
**File:** `logout.php`
- **Action:** Destroy session
- **Redirect:** login.php

---

## 5. SECURITY FEATURES

### 5.1 Implemented
- ✅ Password hashing (password_verify)
- ✅ Session-based authentication
- ✅ Prepared statements (PDO)
- ✅ Session validation on protected pages

### 5.2 Recommendations
- Add CSRF token protection
- Implement session timeout
- Add login attempt limiting
- Add password reset functionality

---

## 6. ENHANCEMENT SCOPE (New Development)

### 6.1 Additional Features Required
- ❌ Order management view
- ❌ Payment verification interface
- ❌ Sales reports dashboard
- ❌ User management view
- ❌ Book pricing management

### 6.2 New Pages to Add
1. `admin-orders.php` - Order management
2. `admin-payments.php` - Payment transactions
3. `admin-reports.php` - Sales reports
4. `verify-payment.php` - Payment verification

### 6.3 Modifications Required
**admin.php:**
- Add navigation links for orders
- Add navigation links for payments
- Add navigation links for reports
- Add price column in books table

---

## 7. TESTING CHECKLIST

### 7.1 Existing Features
- ✅ Admin can login with valid credentials
- ✅ Invalid credentials show error
- ✅ Session persists across pages
- ✅ Logout destroys session
- ✅ Protected pages redirect to login

### 7.2 New Features Testing
- ⏳ Admin can access order management
- ⏳ Admin can verify payments
- ⏳ Admin can view reports
- ⏳ Admin can manage book prices

---

## 8. API ENDPOINTS

### 8.1 Existing
- `POST php/auth.php` - Admin login

### 8.2 Required (New)
- `POST php/verify-payment.php` - Verify payment
- `POST php/update-order-status.php` - Update order
- `GET php/generate-report.php` - Generate reports

---

## 9. MAINTENANCE NOTES

### 9.1 Known Issues
- None reported

### 9.2 Future Improvements
- Multi-admin support
- Role-based access control
- Activity logging
- Two-factor authentication

---

**Last Updated:** 2024  
**Module Owner:** Admin Team
