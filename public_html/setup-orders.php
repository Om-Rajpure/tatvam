<?php
session_start();
include "db_conn.php";

echo "<h2>Module 07 - Orders Setup</h2>";

// Create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo "<p style='color: green;'>✓ Orders table created/verified</p>";
} else {
    echo "<p style='color: red;'>✗ Orders table error: " . $conn->error . "</p>";
}

// Create order_items table
$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    order_id INT(11) NOT NULL,
    book_id INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo "<p style='color: green;'>✓ Order items table created/verified</p>";
} else {
    echo "<p style='color: red;'>✗ Order items table error: " . $conn->error . "</p>";
}

// Create indexes
$indexes = [
    "CREATE UNIQUE INDEX idx_order_number ON orders(order_number)",
    "CREATE INDEX idx_user_orders ON orders(user_id)",
    "CREATE INDEX idx_order_status ON orders(status)",
    "CREATE INDEX idx_order_items ON order_items(order_id)"
];

foreach ($indexes as $index) {
    $conn->query($index);
}
echo "<p style='color: green;'>✓ Indexes created/verified</p>";

echo "<hr>";
echo "<h3>Setup Complete!</h3>";
echo "<p><a href='index.php'>Go to Homepage</a></p>";
echo "<p><a href='cart.php'>Go to Cart</a></p>";
echo "<p><a href='my-orders.php'>View My Orders</a></p>";
?>
