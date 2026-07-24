<?php
session_start();
include "db_conn.php";

echo "<h2>Debug Information</h2>";

// Check session
echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✓ User is logged in</p>";
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p>User Type: " . ($_SESSION['user_type'] ?? 'NOT SET') . "</p>";
    echo "<p>User Name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "</p>";
} else {
    echo "<p style='color: red;'>✗ User is NOT logged in</p>";
}

// Check database connection
if ($conn) {
    echo "<p style='color: green;'>✓ Database connected</p>";
} else {
    echo "<p style='color: red;'>✗ Database NOT connected</p>";
}

// Check cart table
try {
    $sql = "SELECT * FROM cart LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $carts = $stmt->fetchAll();
    echo "<h3>Cart Table (Sample):</h3>";
    echo "<pre>";
    print_r($carts);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Cart table error: " . $e->getMessage() . "</p>";
}

// Check books with price > 0
try {
    $sql = "SELECT id, title, price FROM books WHERE price > 0 LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $books = $stmt->fetchAll();
    echo "<h3>Paid Books (Sample):</h3>";
    echo "<pre>";
    print_r($books);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Books table error: " . $e->getMessage() . "</p>";
}

// Test add to cart if user is logged in and book_id provided
if (isset($_SESSION['user_id']) && isset($_GET['test_book_id'])) {
    $book_id = intval($_GET['test_book_id']);
    $user_id = $_SESSION['user_id'];
    
    echo "<h3>Testing Add to Cart:</h3>";
    echo "<p>User ID: $user_id</p>";
    echo "<p>Book ID: $book_id</p>";
    
    // Check if book exists
    $sql = "SELECT * FROM books WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    
    if ($book) {
        echo "<p style='color: green;'>✓ Book found: " . $book['title'] . " (Price: ₹" . $book['price'] . ")</p>";
        
        // Try to add to cart
        try {
            $sql = "INSERT INTO cart (user_id, book_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$user_id, $book_id])) {
                echo "<p style='color: green;'>✓ Successfully added to cart!</p>";
            } else {
                echo "<p style='color: red;'>✗ Failed to add to cart</p>";
                print_r($stmt->errorInfo());
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Book not found</p>";
    }
}

echo "<hr>";
echo "<h3>Test Links:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<p><a href='debug-cart.php'>Refresh Debug</a></p>";
    echo "<p><a href='index.php'>Go to Homepage</a></p>";
    echo "<p><a href='cart.php'>View Cart</a></p>";
    
    // Get a paid book for testing
    $sql = "SELECT id, title FROM books WHERE price > 0 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $test_book = $stmt->fetch();
    if ($test_book) {
        echo "<p><a href='php/add-to-cart.php?book_id=" . $test_book['id'] . "'>Test Add to Cart (via handler)</a></p>";
        echo "<p><a href='debug-cart.php?test_book_id=" . $test_book['id'] . "'>Test Add to Cart (direct)</a></p>";
    }
} else {
    echo "<p><a href='user-login.php'>Login First</a></p>";
}
?>
