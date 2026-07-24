# MODULE 07: CHECKOUT & ORDER MANAGEMENT SYSTEM

**Status:** ❌ NEW DEVELOPMENT REQUIRED  
**Version:** 2.0.0  
**Dependencies:** Module 05 (User Management), Module 06 (Shopping Cart)  
**Priority:** HIGH

---

## 1. MODULE OVERVIEW

### 1.1 Purpose
Handle checkout process and order creation for book purchases.

### 1.2 Development Status
- ❌ Checkout page
- ❌ Order creation
- ❌ Order history (user)
- ❌ Order details view
- ❌ Order management (admin)
- ❌ Order status updates

---

## 2. DATABASE SCHEMA

### 2.1 New Tables Required

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

**Indexes:**
```sql
CREATE UNIQUE INDEX idx_order_number ON orders(order_number);
CREATE INDEX idx_user_orders ON orders(user_id);
CREATE INDEX idx_order_status ON orders(status);
CREATE INDEX idx_order_items ON order_items(order_id);
```

---

## 3. FILES TO CREATE

### 3.1 Frontend Files (User)
1. `checkout.php` - Checkout page with order summary
2. `my-orders.php` - User order history
3. `order-details.php` - Single order view

### 3.2 Frontend Files (Admin)
1. `admin-orders.php` - All orders management
2. `order-invoice.php` - Order invoice/receipt

### 3.3 Backend Files
1. `php/create-order.php` - Create order from cart
2. `php/update-order-status.php` - Update order status (admin)
3. `php/func-order.php` - Order helper functions

---

## 4. FUNCTIONAL SPECIFICATIONS

### 4.1 Checkout Process
**File:** `checkout.php`

**Prerequisites:**
- User must be logged in
- Cart must not be empty

**Display:**
```php
- User details (name, email, phone)
- Order summary
- List of books in cart
- Individual prices
- Total amount
- Terms and conditions checkbox
- Proceed to payment button
```

**Validation:**
```php
- Cart not empty
- All books still available
- Prices haven't changed
```

### 4.2 Create Order
**File:** `php/create-order.php`

**Process:**
```php
1. Validate user session
2. Validate cart not empty
3. Generate unique order number
4. Calculate total amount
5. Begin transaction
6. Insert into orders table (status: pending)
7. Insert cart items into order_items
8. Commit transaction
9. Store order_id in session
10. Redirect to payment page
```

**Order Number Format:**
```php
ORD + YYYYMMDD + Random(4 digits)
Example: ORD202401150001
```

**Transaction Handling:**
```php
try {
    $conn->beginTransaction();
    // Create order
    // Create order items
    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    // Handle error
}
```

### 4.3 My Orders (User)
**File:** `my-orders.php`

**Display:**
```php
- Order number
- Order date
- Total amount
- Status badge (color-coded)
- Number of books
- View details button
```

**Status Colors:**
```php
- Pending: Yellow/Warning
- Completed: Green/Success
- Failed: Red/Danger
- Refunded: Blue/Info
```

**Sorting:**
```php
- Default: Latest first
- Filter by status
```

### 4.4 Order Details (User)
**File:** `order-details.php?id=`

**Display:**
```php
- Order number
- Order date
- Status
- Payment details
- Transaction ID
- Books list with covers
- Individual prices
- Total amount
- Download buttons (if completed)
- Invoice download button
```

**Access Control:**
```php
- Verify order belongs to logged-in user
- Redirect if unauthorized
```

### 4.5 Admin Orders Management
**File:** `admin-orders.php`

**Display:**
```php
- All orders from all users
- Order number
- User name and email
- Order date
- Total amount
- Status
- Payment status
- Action buttons (view, verify, update status)
```

**Features:**
```php
- Search by order number
- Filter by status
- Filter by date range
- Sort by date/amount
- Pagination
```

### 4.6 Update Order Status (Admin)
**File:** `php/update-order-status.php`

**Input:**
- Order ID
- New Status

**Process:**
```php
1. Validate admin session
2. Validate order exists
3. Update order status
4. If status = 'completed':
   - Grant download access
5. If status = 'failed' or 'refunded':
   - Revoke download access
6. Log status change
7. Redirect with success message
```

---

## 5. HELPER FUNCTIONS

### 5.1 Required Functions (php/func-order.php)

```php
// Generate unique order number
function generate_order_number($con) {
    do {
        $number = 'ORD' . date('Ymd') . rand(1000, 9999);
        $sql = "SELECT COUNT(*) FROM orders WHERE order_number=?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$number]);
    } while ($stmt->fetchColumn() > 0);
    return $number;
}

// Create order from cart
function create_order($con, $user_id) {
    $conn->beginTransaction();
    try {
        // Generate order number
        $order_number = generate_order_number($con);
        
        // Get cart total
        $total = get_cart_total($con, $user_id);
        
        // Create order
        $sql = "INSERT INTO orders (user_id, order_number, total_amount, status) 
                VALUES (?, ?, ?, 'pending')";
        $stmt = $con->prepare($sql);
        $stmt->execute([$user_id, $order_number, $total]);
        $order_id = $con->lastInsertId();
        
        // Get cart items
        $cart_items = get_cart_items($con, $user_id);
        
        // Create order items
        $sql = "INSERT INTO order_items (order_id, book_id, price) VALUES (?, ?, ?)";
        $stmt = $con->prepare($sql);
        foreach ($cart_items as $item) {
            $stmt->execute([$order_id, $item['book_id'], $item['price']]);
        }
        
        $conn->commit();
        return ['status' => 'success', 'order_id' => $order_id, 'order_number' => $order_number];
    } catch (Exception $e) {
        $conn->rollBack();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

// Get user orders
function get_user_orders($con, $user_id) {
    $sql = "SELECT o.*, COUNT(oi.id) as book_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Get single order with items
function get_order($con, $order_id) {
    $sql = "SELECT o.*, u.full_name, u.email, u.phone
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetch();
}

// Get order items with book details
function get_order_items($con, $order_id) {
    $sql = "SELECT oi.*, b.title, b.cover, b.file, 
                   a.name as author_name
            FROM order_items oi
            JOIN books b ON oi.book_id = b.id
            JOIN authors a ON b.author_id = a.id
            WHERE oi.order_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}

// Get all orders (admin)
function get_all_orders($con, $status = null) {
    $sql = "SELECT o.*, u.full_name, u.email, 
                   COUNT(oi.id) as book_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id";
    
    if ($status) {
        $sql .= " WHERE o.status = ?";
    }
    
    $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
    
    $stmt = $con->prepare($sql);
    if ($status) {
        $stmt->execute([$status]);
    } else {
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

// Update order status
function update_order_status($con, $order_id, $status) {
    $sql = "UPDATE orders SET status=?, updated_at=NOW() WHERE id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$status, $order_id]);
}

// Check if user owns order
function user_owns_order($con, $user_id, $order_id) {
    $sql = "SELECT COUNT(*) FROM orders WHERE id=? AND user_id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$order_id, $user_id]);
    return $stmt->fetchColumn() > 0;
}

// Get order statistics
function get_order_stats($con) {
    $sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status='completed' THEN total_amount ELSE 0 END) as total_revenue
            FROM orders";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
}
```

---

## 6. UI SPECIFICATIONS

### 6.1 Checkout Page (checkout.php)

```html
<div class="container">
    <h2>Checkout</h2>
    
    <div class="row">
        <div class="col-md-8">
            <h4>Order Summary</h4>
            <table class="table">
                <?php foreach ($cart_items as $item) { ?>
                <tr>
                    <td><img src="uploads/cover/<?=$item['cover']?>" width="50"></td>
                    <td><?=$item['title']?></td>
                    <td>₹<?=number_format($item['price'], 2)?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td colspan="2"><strong>Total:</strong></td>
                    <td><strong>₹<?=number_format($cart_total, 2)?></strong></td>
                </tr>
            </table>
        </div>
        
        <div class="col-md-4">
            <h4>Billing Details</h4>
            <p><strong>Name:</strong> <?=$user['full_name']?></p>
            <p><strong>Email:</strong> <?=$user['email']?></p>
            <p><strong>Phone:</strong> <?=$user['phone']?></p>
            
            <form method="POST" action="php/create-order.php">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to terms and conditions
                    </label>
                </div>
                <button type="submit" class="btn btn-success btn-block mt-3">
                    Proceed to Payment
                </button>
            </form>
        </div>
    </div>
</div>
```

### 6.2 My Orders Page (my-orders.php)

```html
<div class="container">
    <h2>My Orders</h2>
    
    <?php if ($orders == 0) { ?>
        <div class="alert alert-info">
            <p>You haven't placed any orders yet</p>
            <a href="index.php" class="btn btn-primary">Browse Books</a>
        </div>
    <?php } else { ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Date</th>
                    <th>Books</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) { ?>
                <tr>
                    <td><?=$order['order_number']?></td>
                    <td><?=date('d M Y', strtotime($order['created_at']))?></td>
                    <td><?=$order['book_count']?> book(s)</td>
                    <td>₹<?=number_format($order['total_amount'], 2)?></td>
                    <td>
                        <span class="badge bg-<?=get_status_color($order['status'])?>">
                            <?=ucfirst($order['status'])?>
                        </span>
                    </td>
                    <td>
                        <a href="order-details.php?id=<?=$order['id']?>" 
                           class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
```

---

## 7. BUSINESS RULES

### 7.1 Order Creation Rules
- Cart must not be empty
- User must be logged in
- All books must be available
- Order number must be unique

### 7.2 Order Status Flow
```
pending → completed (after payment verification)
pending → failed (if payment fails)
completed → refunded (if refund issued)
```

### 7.3 Download Access Rules
- Granted when status = 'completed'
- Revoked when status = 'failed' or 'refunded'

---

## 8. TESTING CHECKLIST

### 8.1 Checkout Testing
- ⏳ User can view checkout page
- ⏳ Order summary displays correctly
- ⏳ Total calculates correctly
- ⏳ Empty cart redirects
- ⏳ Guest user redirects to login

### 8.2 Order Creation Testing
- ⏳ Order creates successfully
- ⏳ Unique order number generated
- ⏳ Order items created correctly
- ⏳ Transaction rollback on error
- ⏳ Redirect to payment page

### 8.3 Order History Testing
- ⏳ User can view their orders
- ⏳ Orders display correctly
- ⏳ Status badges show correctly
- ⏳ Empty state displays
- ⏳ Order details accessible

### 8.4 Admin Testing
- ⏳ Admin can view all orders
- ⏳ Admin can filter by status
- ⏳ Admin can update status
- ⏳ Search works correctly

---

## 9. API ENDPOINTS

### 9.1 New Endpoints
- `POST php/create-order.php` - Create order
- `POST php/update-order-status.php` - Update status (admin)
- `GET php/get-order-invoice.php?id=` - Generate invoice

---

## 10. INTEGRATION POINTS

### 10.1 Required Integrations
- Module 06: Shopping Cart (cart items)
- Module 08: Payment System (order payment)
- Module 10: Download Management (access control)

---

## 11. DEVELOPMENT TIMELINE

**Estimated Time:** 5-6 days

- Day 1-2: Database and order creation
- Day 3: User order history
- Day 4: Admin order management
- Day 5-6: Testing and integration

---

**Last Updated:** 2024  
**Module Owner:** Order Management Team  
**Status:** Ready for Development
