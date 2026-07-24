# MODULE 09: DOWNLOAD MANAGEMENT SYSTEM

**Status:** ❌ NEW DEVELOPMENT REQUIRED  
**Version:** 2.0.0  
**Dependencies:** Module 07 (Orders), Module 08 (Payment)  
**Priority:** HIGH

---

## 1. MODULE OVERVIEW

### 1.1 Purpose
Secure download system with purchase verification and access control.

### 1.2 Development Status
- ❌ Secure download handler
- ❌ Purchase verification
- ❌ My Library page
- ❌ Download tracking
- ❌ Re-download capability

---

## 2. DATABASE SCHEMA

### 2.1 New Table Required

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
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (user_id, book_id, order_id)
);
```

---

## 3. FILES TO CREATE

### 3.1 Frontend Files
1. `my-library.php` - User's purchased books library
2. `download.php` - Secure download handler

### 3.2 Backend Files
1. `php/func-download.php` - Download helper functions
2. `php/grant-access.php` - Grant download access
3. `php/revoke-access.php` - Revoke download access

---

## 4. FUNCTIONAL SPECIFICATIONS

### 4.1 Grant Download Access
**Triggered when:** Order status = 'completed'

**Process:**
```php
1. Get order items
2. For each book in order:
   - Create download access record
   - Set download_count = 0
3. Return success
```

**Code:**
```php
function grant_download_access($con, $order_id) {
    // Get order details
    $order = get_order($con, $order_id);
    $user_id = $order['user_id'];
    
    // Get order items
    $items = get_order_items($con, $order_id);
    
    // Grant access for each book
    $sql = "INSERT INTO downloads (user_id, book_id, order_id) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE order_id=VALUES(order_id)";
    $stmt = $con->prepare($sql);
    
    foreach ($items as $item) {
        $stmt->execute([$user_id, $item['book_id'], $order_id]);
    }
    
    return true;
}
```

### 4.2 Secure Download Handler
**File:** `download.php?id=BOOK_ID`

**Validation:**
```php
1. Check user is logged in
2. Check book exists
3. Check book price:
   - If free (price = 0): Allow download
   - If paid (price > 0): Check purchase
4. Verify download access exists
5. Verify order status = 'completed'
6. Increment download count
7. Update last_downloaded timestamp
8. Serve file securely
```

**Code:**
```php
session_start();
include "db_conn.php";
include "php/func-download.php";
include "php/func-book.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php?redirect=download.php?id=" . $_GET['id']);
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = $_GET['id'];

// Get book
$book = get_book($conn, $book_id);

if (!$book) {
    die("Book not found");
}

// Check if free book
if ($book['price'] == 0) {
    // Allow free download
    serve_file($book['file'], $book['title']);
    exit;
}

// Check purchase
if (!has_download_access($conn, $user_id, $book_id)) {
    die("You don't have access to this book. Please purchase first.");
}

// Track download
track_download($conn, $user_id, $book_id);

// Serve file
serve_file($book['file'], $book['title']);

function serve_file($filename, $title) {
    $filepath = "uploads/files/" . $filename;
    
    if (!file_exists($filepath)) {
        die("File not found");
    }
    
    // Set headers
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $title . '.pdf"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache');
    
    // Output file
    readfile($filepath);
    exit;
}
```

### 4.3 My Library Page
**File:** `my-library.php`

**Display:**
```php
- All purchased books
- Book cover
- Title
- Author
- Purchase date
- Order number
- Download count
- Download button
- Last downloaded date
```

**Query:**
```php
SELECT b.*, d.download_count, d.last_downloaded, 
       o.order_number, o.created_at as purchase_date,
       a.name as author_name
FROM downloads d
JOIN books b ON d.book_id = b.id
JOIN orders o ON d.order_id = o.id
JOIN authors a ON b.author_id = a.id
WHERE d.user_id = ? AND o.status = 'completed'
ORDER BY d.created_at DESC
```

---

## 5. HELPER FUNCTIONS

### 5.1 Required Functions (php/func-download.php)

```php
// Check if user has download access
function has_download_access($con, $user_id, $book_id) {
    $sql = "SELECT COUNT(*) FROM downloads d
            JOIN orders o ON d.order_id = o.id
            WHERE d.user_id=? AND d.book_id=? AND o.status='completed'";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id, $book_id]);
    return $stmt->fetchColumn() > 0;
}

// Grant download access
function grant_download_access($con, $order_id) {
    $order = get_order($con, $order_id);
    $user_id = $order['user_id'];
    $items = get_order_items($con, $order_id);
    
    $sql = "INSERT INTO downloads (user_id, book_id, order_id) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE order_id=VALUES(order_id)";
    $stmt = $con->prepare($sql);
    
    foreach ($items as $item) {
        $stmt->execute([$user_id, $item['book_id'], $order_id]);
    }
    
    return true;
}

// Revoke download access
function revoke_download_access($con, $order_id) {
    $sql = "DELETE FROM downloads WHERE order_id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$order_id]);
}

// Track download
function track_download($con, $user_id, $book_id) {
    $sql = "UPDATE downloads 
            SET download_count = download_count + 1, 
                last_downloaded = NOW() 
            WHERE user_id=? AND book_id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$user_id, $book_id]);
}

// Get user library
function get_user_library($con, $user_id) {
    $sql = "SELECT b.*, d.download_count, d.last_downloaded, 
                   o.order_number, o.created_at as purchase_date,
                   a.name as author_name, c.name as category_name
            FROM downloads d
            JOIN books b ON d.book_id = b.id
            JOIN orders o ON d.order_id = o.id
            JOIN authors a ON b.author_id = a.id
            JOIN categories c ON b.category_id = c.id
            WHERE d.user_id = ? AND o.status = 'completed'
            ORDER BY d.created_at DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Get download statistics
function get_download_stats($con, $book_id) {
    $sql = "SELECT 
                COUNT(DISTINCT user_id) as unique_downloads,
                SUM(download_count) as total_downloads
            FROM downloads 
            WHERE book_id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$book_id]);
    return $stmt->fetch();
}
```

---

## 6. UI SPECIFICATIONS

### 6.1 My Library Page (my-library.php)

```html
<div class="container">
    <h2>My Library</h2>
    
    <?php if ($library == 0) { ?>
        <div class="alert alert-info">
            <p>You haven't purchased any books yet</p>
            <a href="index.php" class="btn btn-primary">Browse Books</a>
        </div>
    <?php } else { ?>
        <div class="row">
            <?php foreach ($library as $book) { ?>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="uploads/cover/<?=$book['cover']?>" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title"><?=$book['title']?></h5>
                        <p class="card-text">
                            <small>By: <?=$book['author_name']?></small><br>
                            <small>Purchased: <?=date('d M Y', strtotime($book['purchase_date']))?></small><br>
                            <small>Downloads: <?=$book['download_count']?></small>
                        </p>
                        <a href="download.php?id=<?=$book['id']?>" 
                           class="btn btn-success btn-block">
                            <i class="bi bi-download"></i> Download
                        </a>
                        <?php if ($book['last_downloaded']) { ?>
                        <small class="text-muted">
                            Last: <?=date('d M Y', strtotime($book['last_downloaded']))?>
                        </small>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
```

---

## 7. BUSINESS RULES

### 7.1 Download Access Rules
- Free books: Anyone can download
- Paid books: Only purchasers can download
- Access granted when order status = 'completed'
- Access revoked when order status = 'failed' or 'refunded'
- Unlimited re-downloads for purchased books

### 7.2 Download Tracking
- Track download count per user per book
- Track last download timestamp
- No download limit (unlimited)

---

## 8. TESTING CHECKLIST

### 8.1 Download Access Testing
- ⏳ Free books download without login
- ⏳ Paid books require login
- ⏳ Unpurchased books show error
- ⏳ Purchased books download successfully
- ⏳ Download count increments
- ⏳ Last downloaded updates

### 8.2 Library Testing
- ⏳ Library shows purchased books
- ⏳ Empty library shows message
- ⏳ Download buttons work
- ⏳ Statistics display correctly
- ⏳ Re-download works

---

## 9. DEVELOPMENT TIMELINE

**Estimated Time:** 3-4 days

- Day 1: Database and access functions
- Day 2: Secure download handler
- Day 3: My Library page
- Day 4: Testing and integration

---

**Last Updated:** 2024  
**Module Owner:** Download Management Team  
**Status:** Ready for Development

---

# MODULE 10: ADMIN REPORTS & ANALYTICS

**Status:** ❌ NEW DEVELOPMENT REQUIRED  
**Version:** 2.0.0  
**Dependencies:** Module 07 (Orders), Module 08 (Payment)  
**Priority:** MEDIUM

---

## 1. MODULE OVERVIEW

### 1.1 Purpose
Provide sales reports and analytics for admin decision making.

### 1.2 Development Status
- ❌ Sales dashboard
- ❌ Revenue reports
- ❌ Order statistics
- ❌ Payment transactions
- ❌ Export to CSV

---

## 2. FILES TO CREATE

### 2.1 Frontend Files
1. `admin-reports.php` - Reports dashboard
2. `admin-payments.php` - Payment transactions

### 2.2 Backend Files
1. `php/func-reports.php` - Report helper functions
2. `php/export-csv.php` - CSV export handler

---

## 3. FUNCTIONAL SPECIFICATIONS

### 3.1 Sales Dashboard
**File:** `admin-reports.php`

**Display:**
```php
- Total revenue
- Total orders
- Total books sold
- Total users
- Pending payments count
- Today's sales
- This month's sales
- Top selling books
- Recent orders
```

### 3.2 Revenue Report
**Filters:**
- Date range
- Status filter
- Export to CSV

**Metrics:**
```php
- Total revenue
- Average order value
- Orders count
- Books sold
- Revenue by date
- Revenue by category
```

### 3.3 Payment Transactions
**File:** `admin-payments.php`

**Display:**
```php
- All payment records
- Transaction ID
- Order number
- User details
- Amount
- Status
- Payment date
- Verified by
- Filter by status
```

---

## 4. HELPER FUNCTIONS

### 4.1 Required Functions (php/func-reports.php)

```php
// Get dashboard statistics
function get_dashboard_stats($con) {
    $sql = "SELECT 
                COUNT(DISTINCT o.id) as total_orders,
                COUNT(DISTINCT o.user_id) as total_customers,
                SUM(CASE WHEN o.status='completed' THEN o.total_amount ELSE 0 END) as total_revenue,
                SUM(CASE WHEN o.status='pending' THEN 1 ELSE 0 END) as pending_orders,
                COUNT(DISTINCT oi.book_id) as books_sold
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
}

// Get revenue by date range
function get_revenue_by_date($con, $start_date, $end_date) {
    $sql = "SELECT 
                DATE(created_at) as date,
                COUNT(*) as orders,
                SUM(total_amount) as revenue
            FROM orders
            WHERE status='completed' 
            AND created_at BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY date DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute([$start_date, $end_date]);
    return $stmt->fetchAll();
}

// Get top selling books
function get_top_selling_books($con, $limit = 10) {
    $sql = "SELECT b.title, a.name as author, 
                   COUNT(oi.id) as sales_count,
                   SUM(oi.price) as total_revenue
            FROM order_items oi
            JOIN books b ON oi.book_id = b.id
            JOIN authors a ON b.author_id = a.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status='completed'
            GROUP BY oi.book_id
            ORDER BY sales_count DESC
            LIMIT ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}
```

---

## 5. DEVELOPMENT TIMELINE

**Estimated Time:** 4-5 days

- Day 1-2: Dashboard statistics
- Day 3: Revenue reports
- Day 4: Payment transactions
- Day 5: CSV export and testing

---

**Last Updated:** 2024  
**Module Owner:** Analytics Team  
**Status:** Ready for Development
