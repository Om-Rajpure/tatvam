# MODULE 05: USER MANAGEMENT SYSTEM

**Status:** ❌ NEW DEVELOPMENT REQUIRED  
**Version:** 2.0.0  
**Dependencies:** None  
**Priority:** HIGH

---

## 1. MODULE OVERVIEW

### 1.1 Purpose
Customer registration, authentication, and profile management for purchasing books.

### 1.2 Development Status
- ❌ User registration
- ❌ User login/logout
- ❌ User profile management
- ❌ Password reset
- ❌ Session management

---

## 2. DATABASE SCHEMA

### 2.1 New Table Required

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

**Indexes:**
```sql
CREATE UNIQUE INDEX idx_email ON users(email);
CREATE INDEX idx_phone ON users(phone);
```

---

## 3. FILES TO CREATE

### 3.1 Frontend Files
1. `register.php` - User registration form
2. `user-login.php` - User login form
3. `user-profile.php` - User profile view/edit
4. `forgot-password.php` - Password reset request (Optional)

### 3.2 Backend Files
1. `php/user-register.php` - Registration handler
2. `php/user-auth.php` - Login authentication
3. `php/user-logout.php` - Logout handler
4. `php/update-profile.php` - Profile update handler
5. `php/func-user.php` - User helper functions

---

## 4. FUNCTIONAL SPECIFICATIONS

### 4.1 User Registration
**File:** `register.php` → `php/user-register.php`

**Input Fields:**
- Full Name (required)
- Email (required, unique)
- Phone (required, 10 digits)
- Password (required, min 6 chars)
- Confirm Password (required, must match)

**Validation:**
```php
- Full name not empty
- Email format valid
- Email not already registered
- Phone 10 digits numeric
- Password minimum 6 characters
- Password and confirm password match
```

**Process:**
```php
1. Validate all inputs
2. Check email uniqueness
3. Hash password using password_hash()
4. Insert user to database
5. Create session (auto-login)
6. Redirect to homepage
```

**Session Variables:**
```php
$_SESSION['user_id']
$_SESSION['user_email']
$_SESSION['user_name']
$_SESSION['user_type'] = 'customer'
```

### 4.2 User Login
**File:** `user-login.php` → `php/user-auth.php`

**Input Fields:**
- Email (required)
- Password (required)

**Validation:**
```php
- Email not empty
- Password not empty
- Credentials match database
```

**Process:**
```php
1. Validate inputs
2. Query user by email
3. Verify password using password_verify()
4. Create session
5. Redirect to homepage or cart
```

### 4.3 User Logout
**File:** `php/user-logout.php`

**Process:**
```php
1. Destroy user session
2. Redirect to homepage
```

### 4.4 User Profile
**File:** `user-profile.php`

**Display:**
- Full Name
- Email (read-only)
- Phone
- Member since date
- Total orders
- Total spent

**Edit Options:**
- Update full name
- Update phone
- Change password

### 4.5 Update Profile
**File:** `php/update-profile.php`

**Input:**
- Full Name
- Phone
- Current Password (for verification)
- New Password (optional)

**Validation:**
```php
- Verify current password
- Validate new data
- Update database
```

---

## 5. HELPER FUNCTIONS

### 5.1 Required Functions (php/func-user.php)

```php
// Get user by ID
function get_user($con, $id) {
    $sql = "SELECT * FROM users WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Get user by email
function get_user_by_email($con, $email) {
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetch();
}

// Check if email exists
function email_exists($con, $email) {
    $sql = "SELECT COUNT(*) FROM users WHERE email=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetchColumn() > 0;
}

// Update user profile
function update_user($con, $id, $name, $phone) {
    $sql = "UPDATE users SET full_name=?, phone=? WHERE id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$name, $phone, $id]);
}

// Change password
function change_password($con, $id, $new_password) {
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password=? WHERE id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$hashed, $id]);
}

// Get user statistics
function get_user_stats($con, $user_id) {
    $sql = "SELECT 
                COUNT(o.id) as total_orders,
                COALESCE(SUM(o.total_amount), 0) as total_spent
            FROM orders o
            WHERE o.user_id=? AND o.status='completed'";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}
```

---

## 6. SECURITY REQUIREMENTS

### 6.1 Password Security
- Use `password_hash()` with PASSWORD_DEFAULT
- Use `password_verify()` for authentication
- Minimum 6 characters
- Store only hashed passwords

### 6.2 Session Security
- Regenerate session ID on login
- Set session timeout (30 minutes)
- Validate session on each request
- Separate user and admin sessions

### 6.3 Input Validation
- Sanitize all inputs
- Validate email format
- Validate phone format
- Prevent SQL injection (prepared statements)
- Prevent XSS (htmlspecialchars)

### 6.4 CSRF Protection
```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validate token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token validation failed');
}
```

---

## 7. UI SPECIFICATIONS

### 7.1 Registration Form (register.php)

```html
<form method="POST" action="php/user-register.php">
    <input type="text" name="full_name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="tel" name="phone" placeholder="Phone (10 digits)" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <button type="submit">Register</button>
    <a href="user-login.php">Already have account? Login</a>
</form>
```

### 7.2 Login Form (user-login.php)

```html
<form method="POST" action="php/user-auth.php">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <a href="register.php">Don't have account? Register</a>
</form>
```

### 7.3 Profile Page (user-profile.php)

```html
<div class="profile-info">
    <h3><?=$user['full_name']?></h3>
    <p>Email: <?=$user['email']?></p>
    <p>Phone: <?=$user['phone']?></p>
    <p>Member Since: <?=date('M Y', strtotime($user['created_at']))?></p>
</div>

<div class="profile-stats">
    <p>Total Orders: <?=$stats['total_orders']?></p>
    <p>Total Spent: ₹<?=number_format($stats['total_spent'], 2)?></p>
</div>

<form method="POST" action="php/update-profile.php">
    <input type="text" name="full_name" value="<?=$user['full_name']?>">
    <input type="tel" name="phone" value="<?=$user['phone']?>">
    <button type="submit">Update Profile</button>
</form>
```

---

## 8. NAVIGATION INTEGRATION

### 8.1 Update Navigation (index.php, admin.php, etc.)

```php
<?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'customer') { ?>
    <li><a href="cart.php">Cart (<?=$cart_count?>)</a></li>
    <li><a href="my-library.php">My Library</a></li>
    <li><a href="my-orders.php">My Orders</a></li>
    <li><a href="user-profile.php">Profile</a></li>
    <li><a href="php/user-logout.php">Logout</a></li>
<?php } else { ?>
    <li><a href="user-login.php">Login</a></li>
    <li><a href="register.php">Register</a></li>
<?php } ?>
```

---

## 9. TESTING CHECKLIST

### 9.1 Registration Testing
- ⏳ User can register with valid data
- ⏳ Duplicate email shows error
- ⏳ Invalid email format shows error
- ⏳ Password mismatch shows error
- ⏳ Phone validation works
- ⏳ Auto-login after registration

### 9.2 Login Testing
- ⏳ User can login with valid credentials
- ⏳ Invalid credentials show error
- ⏳ Session persists across pages
- ⏳ Redirect to intended page after login

### 9.3 Profile Testing
- ⏳ User can view profile
- ⏳ User can update name and phone
- ⏳ User can change password
- ⏳ Statistics display correctly

### 9.4 Security Testing
- ⏳ Passwords are hashed
- ⏳ SQL injection prevented
- ⏳ XSS prevented
- ⏳ Session hijacking prevented

---

## 10. API ENDPOINTS

### 10.1 New Endpoints
- `POST php/user-register.php` - User registration
- `POST php/user-auth.php` - User login
- `GET php/user-logout.php` - User logout
- `POST php/update-profile.php` - Update profile
- `POST php/change-password.php` - Change password

---

## 11. INTEGRATION POINTS

### 11.1 Required Integrations
- Module 06: Shopping Cart (user_id)
- Module 07: Checkout & Orders (user_id)
- Module 08: Payment System (user_id)
- Module 10: Download Management (user_id)

---

## 12. ERROR MESSAGES

### 12.1 Registration Errors
- "Full name is required"
- "Email is required"
- "Invalid email format"
- "Email already registered"
- "Phone number must be 10 digits"
- "Password must be at least 6 characters"
- "Passwords do not match"

### 12.2 Login Errors
- "Email is required"
- "Password is required"
- "Invalid email or password"
- "Account not found"

---

## 13. DEVELOPMENT TIMELINE

**Estimated Time:** 5-7 days

- Day 1-2: Database setup and registration
- Day 3-4: Login and session management
- Day 5: Profile management
- Day 6-7: Testing and bug fixes

---

**Last Updated:** 2024  
**Module Owner:** User Management Team  
**Status:** Ready for Development
