# MODULE 06: SHOPPING CART SYSTEM

**Status:** ❌ NEW DEVELOPMENT REQUIRED  
**Version:** 2.0.0  
**Dependencies:** Module 02 (Book Management), Module 05 (User Management)  
**Priority:** HIGH

---

## 1. MODULE OVERVIEW

### 1.1 Purpose
Enable users to add books to cart before purchasing.

### 1.2 Development Status
- ❌ Add to cart functionality
- ❌ View cart
- ❌ Remove from cart
- ❌ Update cart quantities
- ❌ Clear cart
- ❌ Cart item count display

---

## 2. DATABASE SCHEMA

### 2.1 New Table Required

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

**Indexes:**
```sql
CREATE INDEX idx_user_cart ON cart(user_id);
CREATE INDEX idx_book_cart ON cart(book_id);
```

---

## 3. FILES TO CREATE

### 3.1 Frontend Files
1. `cart.php` - Shopping cart page

### 3.2 Backend Files
1. `php/add-to-cart.php` - Add book to cart
2. `php/remove-from-cart.php` - Remove item from cart
3. `php/clear-cart.php` - Clear all cart items
4. `php/func-cart.php` - Cart helper functions

---

## 4. FUNCTIONAL SPECIFICATIONS

### 4.1 Add to Cart
**File:** `php/add-to-cart.php`

**Input:**
- Book ID (from GET/POST)

**Validation:**
```php
- User must be logged in
- Book must exist
- Book price > 0 (paid books only)
- Book not already in cart
- Book not already purchased by user
```

**Process:**
```php
1. Check user authentication
2. Validate book ID
3. Check if book already in cart
4. Check if book already purchased
5. Insert into cart table
6. Return success message
```

**Response:**
```php
Success: "Book added to cart"
Error: "Please login to add items"
Error: "Book already in cart"
Error: "You already own this book"
Error: "Free books don't need cart"
```

### 4.2 View Cart
**File:** `cart.php`

**Display:**
```php
- Cart items list
- Book cover thumbnail
- Book title
- Author name
- Price
- Remove button
- Total amount
- Proceed to checkout button
- Continue shopping link
```

**Empty Cart:**
```php
- "Your cart is empty" message
- Browse books link
```

### 4.3 Remove from Cart
**File:** `php/remove-from-cart.php`

**Input:**
- Cart Item ID or Book ID

**Process:**
```php
1. Verify user owns the cart item
2. Delete from cart table
3. Redirect back to cart
```

### 4.4 Clear Cart
**File:** `php/clear-cart.php`

**Process:**
```php
1. Delete all cart items for user
2. Redirect to cart page
```

### 4.5 Cart Count (Badge)
**Display in Navigation:**
```php
Cart (<?=$cart_count?>)
```

---

## 5. HELPER FUNCTIONS

### 5.1 Required Functions (php/func-cart.php)

```php
// Get user's cart items with book details
function get_cart_items($con, $user_id) {
    $sql = "SELECT c.*, b.title, b.price, b.cover, 
                   a.name as author_name, cat.name as category_name
            FROM cart c
            JOIN books b ON c.book_id = b.id
            JOIN authors a ON b.author_id = a.id
            JOIN categories cat ON b.category_id = cat.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Get cart item count
function get_cart_count($con, $user_id) {
    $sql = "SELECT COUNT(*) FROM cart WHERE user_id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

// Calculate cart total
function get_cart_total($con, $user_id) {
    $sql = "SELECT SUM(b.price) as total
            FROM cart c
            JOIN books b ON c.book_id = b.id
            WHERE c.user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Check if book in cart
function is_in_cart($con, $user_id, $book_id) {
    $sql = "SELECT COUNT(*) FROM cart 
            WHERE user_id=? AND book_id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id, $book_id]);
    return $stmt->fetchColumn() > 0;
}

// Add to cart
function add_to_cart($con, $user_id, $book_id) {
    $sql = "INSERT INTO cart (user_id, book_id) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$user_id, $book_id]);
}

// Remove from cart
function remove_from_cart($con, $user_id, $book_id) {
    $sql = "DELETE FROM cart WHERE user_id=? AND book_id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$user_id, $book_id]);
}

// Clear cart
function clear_cart($con, $user_id) {
    $sql = "DELETE FROM cart WHERE user_id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$user_id]);
}

// Check if user already purchased book
function has_purchased($con, $user_id, $book_id) {
    $sql = "SELECT COUNT(*) FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.user_id=? AND oi.book_id=? 
            AND o.status='completed'";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id, $book_id]);
    return $stmt->fetchColumn() > 0;
}
```

---

## 6. UI SPECIFICATIONS

### 6.1 Add to Cart Button (index.php, search.php, etc.)

```php
<?php if (isset($_SESSION['user_id'])) { ?>
    <?php if ($book['price'] > 0) { ?>
        <?php if (is_in_cart($conn, $_SESSION['user_id'], $book['id'])) { ?>
            <button class="btn btn-secondary" disabled>In Cart</button>
            <a href="cart.php" class="btn btn-primary">View Cart</a>
        <?php } elseif (has_purchased($conn, $_SESSION['user_id'], $book['id'])) { ?>
            <a href="download.php?id=<?=$book['id']?>" class="btn btn-success">Download</a>
        <?php } else { ?>
            <a href="php/add-to-cart.php?book_id=<?=$book['id']?>" 
               class="btn btn-primary">Add to Cart - ₹<?=number_format($book['price'], 2)?></a>
        <?php } ?>
    <?php } else { ?>
        <a href="uploads/files/<?=$book['file']?>" 
           class="btn btn-success" download>Free Download</a>
    <?php } ?>
<?php } else { ?>
    <a href="user-login.php?redirect=index.php" class="btn btn-primary">Login to Purchase</a>
<?php } ?>
```

### 6.2 Cart Page (cart.php)

```html
<div class="container">
    <h2>Shopping Cart</h2>
    
    <?php if ($cart_items == 0) { ?>
        <div class="alert alert-info">
            <p>Your cart is empty</p>
            <a href="index.php" class="btn btn-primary">Browse Books</a>
        </div>
    <?php } else { ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item) { ?>
                <tr>
                    <td><img src="uploads/cover/<?=$item['cover']?>" width="50"></td>
                    <td><?=$item['title']?></td>
                    <td><?=$item['author_name']?></td>
                    <td>₹<?=number_format($item['price'], 2)?></td>
                    <td>
                        <a href="php/remove-from-cart.php?book_id=<?=$item['book_id']?>" 
                           class="btn btn-danger btn-sm">Remove</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Total:</strong></td>
                    <td colspan="2"><strong>₹<?=number_format($cart_total, 2)?></strong></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="cart-actions">
            <a href="php/clear-cart.php" class="btn btn-warning">Clear Cart</a>
            <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
        </div>
    <?php } ?>
</div>
```

### 6.3 Cart Badge in Navigation

```php
<?php 
if (isset($_SESSION['user_id'])) {
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}
?>

<li class="nav-item">
    <a href="cart.php" class="nav-link">
        <i class="bi bi-cart"></i> Cart 
        <?php if ($cart_count > 0) { ?>
            <span class="badge bg-danger"><?=$cart_count?></span>
        <?php } ?>
    </a>
</li>
```

---

## 7. AJAX IMPLEMENTATION (Optional Enhancement)

### 7.1 Add to Cart via AJAX

```javascript
function addToCart(bookId) {
    $.ajax({
        url: 'php/add-to-cart.php',
        method: 'POST',
        data: { book_id: bookId },
        success: function(response) {
            alert('Book added to cart');
            updateCartCount();
        },
        error: function() {
            alert('Error adding to cart');
        }
    });
}

function updateCartCount() {
    $.get('php/get-cart-count.php', function(count) {
        $('.cart-badge').text(count);
    });
}
```

---

## 8. BUSINESS RULES

### 8.1 Cart Restrictions
- Only logged-in users can add to cart
- Only paid books (price > 0) can be added
- Free books bypass cart (direct download)
- Already purchased books cannot be added
- Duplicate books prevented (unique constraint)

### 8.2 Cart Expiration
- Cart items persist until manually removed
- No automatic expiration (optional: add 30-day expiry)

---

## 9. TESTING CHECKLIST

### 9.1 Add to Cart Testing
- ⏳ Logged-in user can add paid book
- ⏳ Guest user redirected to login
- ⏳ Free books don't show add to cart
- ⏳ Already purchased books show download
- ⏳ Duplicate add shows "already in cart"
- ⏳ Cart count updates correctly

### 9.2 Cart Page Testing
- ⏳ Cart items display correctly
- ⏳ Book details accurate
- ⏳ Total calculates correctly
- ⏳ Empty cart shows message
- ⏳ Remove item works
- ⏳ Clear cart works

### 9.3 Integration Testing
- ⏳ Cart persists across sessions
- ⏳ Cart clears after successful order
- ⏳ Purchased books removed from cart

---

## 10. API ENDPOINTS

### 10.1 New Endpoints
- `GET/POST php/add-to-cart.php?book_id=` - Add to cart
- `GET php/remove-from-cart.php?book_id=` - Remove item
- `GET php/clear-cart.php` - Clear cart
- `GET php/get-cart-count.php` - Get cart count (AJAX)

---

## 11. INTEGRATION POINTS

### 11.1 Required Integrations
- Module 02: Book Management (book details, price)
- Module 05: User Management (user authentication)
- Module 07: Checkout System (cart items to order)

---

## 12. ERROR HANDLING

### 12.1 Error Messages
- "Please login to add items to cart"
- "This book is already in your cart"
- "You already own this book"
- "Free books can be downloaded directly"
- "Book not found"
- "Unable to add to cart. Please try again"

---

## 13. PERFORMANCE CONSIDERATIONS

### 13.1 Optimization
- Index on user_id and book_id
- Cache cart count in session
- Lazy load book images
- Pagination for large carts (optional)

---

## 14. DEVELOPMENT TIMELINE

**Estimated Time:** 3-4 days

- Day 1: Database and helper functions
- Day 2: Add to cart functionality
- Day 3: Cart page and remove/clear
- Day 4: Testing and integration

---

**Last Updated:** 2024  
**Module Owner:** E-commerce Team  
**Status:** Ready for Development
