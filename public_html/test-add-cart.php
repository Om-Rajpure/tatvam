<?php
session_start();
include "db_conn.php";

echo "<h2>Add to Cart Test</h2>";

// Display session info
echo "<h3>Session Info:</h3>";
echo "<pre>";
echo "user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "\n";
echo "user_type: " . (isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'NOT SET') . "\n";
echo "user_name: " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'NOT SET') . "\n";
echo "</pre>";

// Get a paid book
$sql = "SELECT id, title, price FROM books WHERE price > 0 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$book = $stmt->fetch();

if ($book) {
    echo "<h3>Test Book:</h3>";
    echo "<p>ID: " . $book['id'] . "</p>";
    echo "<p>Title: " . $book['title'] . "</p>";
    echo "<p>Price: ₹" . $book['price'] . "</p>";
    
    echo "<h3>Test Add to Cart:</h3>";
    echo "<a href='php/add-to-cart.php?book_id=" . $book['id'] . "' class='btn btn-primary'>Add to Cart</a>";
    
    echo "<hr>";
    echo "<h3>Direct Test (Click to add):</h3>";
    
    if (isset($_GET['direct_test']) && isset($_SESSION['user_id'])) {
        include "php/func-cart.php";
        $result = add_to_cart($conn, $_SESSION['user_id'], $book['id']);
        if ($result) {
            echo "<p style='color: green;'>✓ Successfully added to cart!</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to add to cart</p>";
        }
    }
    
    echo "<a href='test-add-cart.php?direct_test=1' class='btn btn-success'>Direct Add Test</a>";
} else {
    echo "<p style='color: red;'>No paid books found. Please add a book with price > 0</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Back to Homepage</a></p>";
echo "<p><a href='user-login.php'>Login</a></p>";
echo "<p><a href='cart.php'>View Cart</a></p>";
?>

<style>
.btn {
    display: inline-block;
    padding: 10px 20px;
    margin: 5px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
.btn-success {
    background: #28a745;
}
</style>
