# MODULE 08: UPI PAYMENT SYSTEM

**Status:** ❌ NEW DEVELOPMENT REQUIRED  
**Version:** 2.0.0  
**Dependencies:** Module 07 (Checkout & Orders)  
**Priority:** CRITICAL

---

## 1. MODULE OVERVIEW

### 1.1 Purpose
Handle UPI payment processing for book purchases with manual verification.

### 1.2 Development Status
- ❌ UPI payment link generation
- ❌ QR code generation
- ❌ Payment page UI
- ❌ Transaction ID submission
- ❌ Payment verification (manual)
- ❌ Payment status tracking

---

## 2. DATABASE SCHEMA

### 2.1 New Table Required

**payments**
```sql
CREATE TABLE payments (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    order_id INT(11) NOT NULL,
    transaction_id VARCHAR(255) UNIQUE,
    upi_id VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'UPI',
    payment_response TEXT,
    verified_by INT(11),
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES admin(id) ON DELETE SET NULL
);
```

**Indexes:**
```sql
CREATE UNIQUE INDEX idx_transaction_id ON payments(transaction_id);
CREATE INDEX idx_order_payment ON payments(order_id);
CREATE INDEX idx_payment_status ON payments(status);
```

---

## 3. FILES TO CREATE

### 3.1 Frontend Files
1. `payment.php` - UPI payment interface
2. `payment-success.php` - Payment success page
3. `payment-failed.php` - Payment failure page
4. `verify-payment.php` - Admin payment verification

### 3.2 Backend Files
1. `php/process-payment.php` - Create payment record
2. `php/submit-transaction.php` - Submit transaction ID
3. `php/verify-payment.php` - Admin verify payment
4. `php/func-payment.php` - Payment helper functions
5. `php/generate-qr.php` - Generate UPI QR code

### 3.3 External Libraries
- PHP QR Code Library: `phpqrcode` or `endroid/qr-code`

---

## 4. UPI PAYMENT SPECIFICATIONS

### 4.1 UPI Payment URL Format

```
upi://pay?pa=MERCHANT_UPI_ID&pn=MERCHANT_NAME&am=AMOUNT&tn=ORDER_NUMBER&cu=INR
```

**Parameters:**
- `pa` - Payee Address (Merchant UPI ID)
- `pn` - Payee Name (Merchant Name)
- `am` - Amount
- `tn` - Transaction Note (Order Number)
- `cu` - Currency (INR)

**Example:**
```
upi://pay?pa=merchant@paytm&pn=BookStore&am=299.00&tn=ORD202401150001&cu=INR
```

### 4.2 Configuration File

**config/payment-config.php:**
```php
<?php
// UPI Payment Configuration
define('MERCHANT_UPI_ID', 'yourmerchant@paytm');
define('MERCHANT_NAME', 'Online Book Store');
define('CURRENCY', 'INR');
define('PAYMENT_MODE', 'UPI');
?>
```

---

## 5. FUNCTIONAL SPECIFICATIONS

### 5.1 Payment Page
**File:** `payment.php`

**Prerequisites:**
- Order must exist
- Order status = 'pending'
- User must own the order

**Display:**
```php
- Order details
- Order number
- Total amount
- UPI QR code
- UPI payment link
- Supported UPI apps icons
- Manual UPI ID input
- Transaction ID input form
- Payment instructions
```

**Payment Flow:**
```
1. Display order summary
2. Generate UPI payment link
3. Generate QR code
4. User scans QR or clicks link
5. User completes payment in UPI app
6. User returns and enters transaction ID
7. Submit transaction ID
8. Wait for admin verification
```

### 5.2 Generate UPI Link
**File:** `php/func-payment.php`

```php
function generate_upi_link($order_number, $amount) {
    $upi_id = MERCHANT_UPI_ID;
    $merchant_name = urlencode(MERCHANT_NAME);
    $amount = number_format($amount, 2, '.', '');
    $order_ref = urlencode($order_number);
    
    $upi_link = "upi://pay?pa={$upi_id}&pn={$merchant_name}&am={$amount}&tn={$order_ref}&cu=INR";
    
    return $upi_link;
}
```

### 5.3 Generate QR Code
**File:** `php/generate-qr.php`

**Using phpqrcode library:**
```php
require_once 'phpqrcode/qrlib.php';

function generate_payment_qr($order_id) {
    // Get order details
    $order = get_order($conn, $order_id);
    
    // Generate UPI link
    $upi_link = generate_upi_link($order['order_number'], $order['total_amount']);
    
    // Generate QR code
    $qr_file = 'uploads/qr/qr_' . $order['order_number'] . '.png';
    QRcode::png($upi_link, $qr_file, QR_ECLEVEL_L, 10);
    
    return $qr_file;
}
```

### 5.4 Create Payment Record
**File:** `php/process-payment.php`

**Process:**
```php
1. Get order details
2. Validate order exists and is pending
3. Create payment record (status: pending)
4. Generate UPI link
5. Generate QR code
6. Display payment page
```

**Code:**
```php
$order_id = $_SESSION['order_id'];
$order = get_order($conn, $order_id);

// Create payment record
$sql = "INSERT INTO payments (order_id, amount, status) VALUES (?, ?, 'pending')";
$stmt = $conn->prepare($sql);
$stmt->execute([$order_id, $order['total_amount']]);
$payment_id = $conn->lastInsertId();

// Store in session
$_SESSION['payment_id'] = $payment_id;

// Redirect to payment page
header("Location: ../payment.php");
```

### 5.5 Submit Transaction ID
**File:** `php/submit-transaction.php`

**Input:**
- Transaction ID (12-digit UPI reference)
- UPI ID (optional)

**Validation:**
```php
- Transaction ID not empty
- Transaction ID format valid
- Transaction ID unique
```

**Process:**
```php
1. Validate transaction ID
2. Update payment record with transaction ID
3. Update payment status to 'pending' (awaiting verification)
4. Send notification to admin (optional)
5. Display waiting message
```

**Code:**
```php
$transaction_id = $_POST['transaction_id'];
$upi_id = $_POST['upi_id'];
$payment_id = $_SESSION['payment_id'];

// Update payment
$sql = "UPDATE payments 
        SET transaction_id=?, upi_id=?, status='pending', updated_at=NOW() 
        WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->execute([$transaction_id, $upi_id, $payment_id]);

// Redirect to waiting page
header("Location: ../payment-pending.php");
```

### 5.6 Admin Payment Verification
**File:** `verify-payment.php` (Admin)

**Display:**
```php
- Pending payments list
- Order number
- User details
- Amount
- Transaction ID
- UPI ID
- Payment date
- Verify/Reject buttons
```

**Verification Process:**
```php
1. Admin checks UPI app for transaction
2. Matches transaction ID and amount
3. Clicks "Verify" or "Reject"
4. System updates payment status
5. System updates order status
6. System grants/denies download access
```

**Code:**
```php
// Verify payment
$payment_id = $_POST['payment_id'];
$action = $_POST['action']; // 'verify' or 'reject'
$admin_id = $_SESSION['user_id'];

if ($action == 'verify') {
    // Update payment status
    $sql = "UPDATE payments 
            SET status='success', verified_by=?, verified_at=NOW() 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$admin_id, $payment_id]);
    
    // Get order ID
    $payment = get_payment($conn, $payment_id);
    
    // Update order status
    update_order_status($conn, $payment['order_id'], 'completed');
    
    // Clear user cart
    clear_cart($conn, $payment['user_id']);
    
    // Grant download access
    grant_download_access($conn, $payment['order_id']);
    
} else {
    // Reject payment
    $sql = "UPDATE payments SET status='failed' WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$payment_id]);
    
    // Update order status
    $payment = get_payment($conn, $payment_id);
    update_order_status($conn, $payment['order_id'], 'failed');
}
```

---

## 6. HELPER FUNCTIONS

### 6.1 Required Functions (php/func-payment.php)

```php
// Generate UPI payment link
function generate_upi_link($order_number, $amount) {
    $upi_id = MERCHANT_UPI_ID;
    $merchant_name = urlencode(MERCHANT_NAME);
    $amount = number_format($amount, 2, '.', '');
    $order_ref = urlencode($order_number);
    return "upi://pay?pa={$upi_id}&pn={$merchant_name}&am={$amount}&tn={$order_ref}&cu=INR";
}

// Create payment record
function create_payment($con, $order_id, $amount) {
    $sql = "INSERT INTO payments (order_id, amount, status) VALUES (?, ?, 'pending')";
    $stmt = $con->prepare($sql);
    $stmt->execute([$order_id, $amount]);
    return $con->lastInsertId();
}

// Get payment by ID
function get_payment($con, $payment_id) {
    $sql = "SELECT p.*, o.order_number, o.user_id, u.full_name, u.email
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            JOIN users u ON o.user_id = u.id
            WHERE p.id = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$payment_id]);
    return $stmt->fetch();
}

// Get payment by order ID
function get_payment_by_order($con, $order_id) {
    $sql = "SELECT * FROM payments WHERE order_id=? ORDER BY created_at DESC LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetch();
}

// Update payment status
function update_payment_status($con, $payment_id, $status, $admin_id = null) {
    if ($admin_id) {
        $sql = "UPDATE payments 
                SET status=?, verified_by=?, verified_at=NOW(), updated_at=NOW() 
                WHERE id=?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$status, $admin_id, $payment_id]);
    } else {
        $sql = "UPDATE payments SET status=?, updated_at=NOW() WHERE id=?";
        $stmt = $con->prepare($sql);
        return $stmt->execute([$status, $payment_id]);
    }
}

// Submit transaction ID
function submit_transaction_id($con, $payment_id, $transaction_id, $upi_id = null) {
    $sql = "UPDATE payments 
            SET transaction_id=?, upi_id=?, status='pending', updated_at=NOW() 
            WHERE id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$transaction_id, $upi_id, $payment_id]);
}

// Get pending payments (admin)
function get_pending_payments($con) {
    $sql = "SELECT p.*, o.order_number, u.full_name, u.email, u.phone
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            JOIN users u ON o.user_id = u.id
            WHERE p.status = 'pending' AND p.transaction_id IS NOT NULL
            ORDER BY p.created_at ASC";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get all payments (admin)
function get_all_payments($con, $status = null) {
    $sql = "SELECT p.*, o.order_number, u.full_name, u.email
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            JOIN users u ON o.user_id = u.id";
    
    if ($status) {
        $sql .= " WHERE p.status = ?";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $con->prepare($sql);
    if ($status) {
        $stmt->execute([$status]);
    } else {
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

// Check transaction ID exists
function transaction_exists($con, $transaction_id) {
    $sql = "SELECT COUNT(*) FROM payments WHERE transaction_id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$transaction_id]);
    return $stmt->fetchColumn() > 0;
}
```

---

## 7. UI SPECIFICATIONS

### 7.1 Payment Page (payment.php)

```html
<div class="container">
    <h2>Complete Payment</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Order Details</h4>
                </div>
                <div class="card-body">
                    <p><strong>Order Number:</strong> <?=$order['order_number']?></p>
                    <p><strong>Amount:</strong> ₹<?=number_format($order['total_amount'], 2)?></p>
                    <p><strong>Books:</strong> <?=$book_count?> item(s)</p>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Payment Instructions</h4>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Scan QR code or click payment link</li>
                        <li>Complete payment in your UPI app</li>
                        <li>Note the Transaction ID (12 digits)</li>
                        <li>Enter Transaction ID below</li>
                        <li>Wait for admin verification</li>
                    </ol>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Pay via UPI</h4>
                </div>
                <div class="card-body text-center">
                    <img src="<?=$qr_code_path?>" alt="UPI QR Code" class="img-fluid mb-3">
                    
                    <p><strong>Scan QR Code</strong></p>
                    <p>Or</p>
                    
                    <a href="<?=$upi_link?>" class="btn btn-primary btn-lg mb-3">
                        Pay with UPI App
                    </a>
                    
                    <div class="upi-apps">
                        <p>Supported Apps:</p>
                        <img src="img/gpay.png" width="50">
                        <img src="img/phonepe.png" width="50">
                        <img src="img/paytm.png" width="50">
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Submit Transaction ID</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="php/submit-transaction.php">
                        <div class="mb-3">
                            <label>Transaction ID (12 digits)</label>
                            <input type="text" name="transaction_id" 
                                   class="form-control" 
                                   placeholder="Enter 12-digit Transaction ID" 
                                   pattern="[0-9]{12}" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label>Your UPI ID (Optional)</label>
                            <input type="text" name="upi_id" 
                                   class="form-control" 
                                   placeholder="yourname@upi">
                        </div>
                        <button type="submit" class="btn btn-success btn-block">
                            Submit for Verification
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 7.2 Payment Pending Page (payment-pending.php)

```html
<div class="container text-center">
    <div class="alert alert-warning">
        <h3>Payment Verification Pending</h3>
        <p>Your transaction ID has been submitted successfully.</p>
        <p>Our team will verify your payment within 24 hours.</p>
        <p><strong>Transaction ID:</strong> <?=$payment['transaction_id']?></p>
        <p><strong>Order Number:</strong> <?=$order['order_number']?></p>
    </div>
    
    <a href="my-orders.php" class="btn btn-primary">View My Orders</a>
    <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
</div>
```

### 7.3 Admin Verification Page (verify-payment.php)

```html
<div class="container">
    <h2>Pending Payment Verifications</h2>
    
    <?php if ($pending_payments == 0) { ?>
        <div class="alert alert-info">No pending payments</div>
    <?php } else { ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Transaction ID</th>
                    <th>UPI ID</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_payments as $payment) { ?>
                <tr>
                    <td><?=$payment['order_number']?></td>
                    <td>
                        <?=$payment['full_name']?><br>
                        <small><?=$payment['email']?></small>
                    </td>
                    <td>₹<?=number_format($payment['amount'], 2)?></td>
                    <td><strong><?=$payment['transaction_id']?></strong></td>
                    <td><?=$payment['upi_id']?></td>
                    <td><?=date('d M Y H:i', strtotime($payment['created_at']))?></td>
                    <td>
                        <form method="POST" action="php/verify-payment.php" style="display:inline">
                            <input type="hidden" name="payment_id" value="<?=$payment['id']?>">
                            <button type="submit" name="action" value="verify" 
                                    class="btn btn-success btn-sm">Verify</button>
                            <button type="submit" name="action" value="reject" 
                                    class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
```

---

## 8. SECURITY CONSIDERATIONS

### 8.1 Payment Security
- Validate all amounts server-side
- Verify transaction ID uniqueness
- Log all payment attempts
- Prevent duplicate submissions
- Secure QR code storage

### 8.2 Transaction Validation
- 12-digit format validation
- Uniqueness check
- Amount verification by admin
- Timeout for pending payments (optional)

---

## 9. TESTING CHECKLIST

### 9.1 Payment Flow Testing
- ⏳ UPI link generates correctly
- ⏳ QR code displays properly
- ⏳ Payment link opens UPI apps
- ⏳ Transaction ID submission works
- ⏳ Duplicate transaction ID rejected
- ⏳ Invalid format rejected

### 9.2 Verification Testing
- ⏳ Admin can view pending payments
- ⏳ Admin can verify payment
- ⏳ Admin can reject payment
- ⏳ Order status updates correctly
- ⏳ Download access granted on success
- ⏳ Cart clears on success

---

## 10. DEVELOPMENT TIMELINE

**Estimated Time:** 5-6 days

- Day 1: Database and payment record creation
- Day 2: UPI link and QR code generation
- Day 3: Payment page UI
- Day 4: Transaction ID submission
- Day 5: Admin verification interface
- Day 6: Testing and integration

---

**Last Updated:** 2024  
**Module Owner:** Payment Team  
**Status:** Ready for Development
