<?php
session_start();
include "db_conn.php";

echo "<h2>Cart System Test & Setup</h2>";

// Check if cart table exists
try {
    $sql = "SHOW TABLES LIKE 'cart'";
    $stmt = $conn->query($sql);
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✓ Cart table exists</p>";
    } else {
        echo "<p style='color:red'>✗ Cart table does NOT exist</p>";
        echo "<p>Creating cart table...</p>";
        
        $sql = "CREATE TABLE IF NOT EXISTS `cart` (
            `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
            `user_id` INT(11) NOT NULL,
            `book_id` INT(11) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
            UNIQUE KEY unique_cart_item (user_id, book_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($conn->exec($sql) !== false) {
            echo "<p style='color:green'>✓ Cart table created successfully</p>";
            
            // Create indexes
            $conn->exec("CREATE INDEX idx_user_cart ON cart(user_id)");
            $conn->exec("CREATE INDEX idx_book_cart ON cart(book_id)");
            echo "<p style='color:green'>✓ Indexes created</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Check if books have price column
try {
    $sql = "SHOW COLUMNS FROM books LIKE 'price'";
    $stmt = $conn->query($sql);
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✓ Books table has price column</p>";
    } else {
        echo "<p style='color:red'>✗ Books table missing price column</p>";
        echo "<p>Adding price column...</p>";
        
        $sql = "ALTER TABLE books ADD COLUMN price DECIMAL(10,2) DEFAULT 0.00 AFTER description";
        if ($conn->exec($sql) !== false) {
            echo "<p style='color:green'>✓ Price column added</p>";
        }
    }
    
    // Update some books with prices
    $sql = "UPDATE books SET price = 299.00 WHERE price = 0 LIMIT 3";
    $conn->exec($sql);
    echo "<p style='color:green'>✓ Sample prices added to books</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Show books with prices
echo "<h3>Books with Prices:</h3>";
$sql = "SELECT id, title, price FROM books LIMIT 10";
$stmt = $conn->query($sql);
$books = $stmt->fetchAll();

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Title</th><th>Price</th></tr>";
foreach ($books as $book) {
    echo "<tr>";
    echo "<td>" . $book['id'] . "</td>";
    echo "<td>" . htmlspecialchars($book['title']) . "</td>";
    echo "<td>₹" . number_format($book['price'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show current user session
echo "<h3>Current Session:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p>User Name: " . $_SESSION['user_name'] . "</p>";
    echo "<p>User Type: " . $_SESSION['user_type'] . "</p>";
} else {
    echo "<p style='color:red'>Not logged in</p>";
    echo "<p><a href='user-login.php'>Login</a> | <a href='register.php'>Register</a></p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Go to Homepage</a> | <a href='cart.php'>View Cart</a></p>";
?>
