<?php
session_start();
include "db_conn.php";

echo "<h2>Module 08 - UPI Payment Setup</h2>";

// Create payments table
$sql = "CREATE TABLE IF NOT EXISTS payments (
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
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo "<p style='color: green;'>✓ Payments table created/verified</p>";
} else {
    echo "<p style='color: red;'>✗ Payments table error: " . $conn->error . "</p>";
}

// Create indexes
$indexes = [
    "CREATE UNIQUE INDEX idx_transaction_id ON payments(transaction_id)",
    "CREATE INDEX idx_order_payment ON payments(order_id)",
    "CREATE INDEX idx_payment_status ON payments(status)"
];

foreach ($indexes as $index) {
    $conn->query($index);
}
echo "<p style='color: green;'>✓ Indexes created/verified</p>";

echo "<hr>";
echo "<h3>Setup Complete!</h3>";
echo "<p>UPI Merchant ID: <strong>" . (defined('MERCHANT_UPI_ID') ? MERCHANT_UPI_ID : 'merchant@paytm') . "</strong></p>";
echo "<p>Update this in <code>config/payment-config.php</code></p>";
echo "<hr>";
echo "<p><a href='index.php'>Go to Homepage</a></p>";
echo "<p><a href='cart.php'>Go to Cart</a></p>";
echo "<p><a href='admin-verify-payments.php'>Admin: Verify Payments</a></p>";
?>
