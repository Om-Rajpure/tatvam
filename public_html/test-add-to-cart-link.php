<!DOCTYPE html>
<html>
<head>
    <title>Add to Cart Test</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .test-box { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
        .info { background: #d1ecf1; }
    </style>
</head>
<body>
    <h1>Add to Cart Diagnostic Test</h1>
    
    <?php
    session_start();
    include "db_conn.php";
    include "php/func-cart.php";
    include "php/func-book.php";
    
    echo "<div class='test-box info'>";
    echo "<h3>1. Session Check</h3>";
    if (isset($_SESSION['user_id'])) {
        echo "<p style='color: green;'>✓ User logged in: ID = " . $_SESSION['user_id'] . "</p>";
        echo "<p>User Type: " . ($_SESSION['user_type'] ?? 'NOT SET') . "</p>";
        echo "<p>User Name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "</p>";
    } else {
        echo "<p style='color: red;'>✗ User NOT logged in</p>";
        echo "<p><a href='user-login.php'>Login Here</a></p>";
    }
    echo "</div>";
    
    // Get a paid book
    $sql = "SELECT id, title, price FROM books WHERE price > 0 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $book = $stmt->fetch();
    
    if ($book) {
        echo "<div class='test-box info'>";
        echo "<h3>2. Test Book</h3>";
        echo "<p>Book ID: " . $book['id'] . "</p>";
        echo "<p>Title: " . $book['title'] . "</p>";
        echo "<p>Price: ₹" . $book['price'] . "</p>";
        echo "</div>";
        
        if (isset($_SESSION['user_id'])) {
            $in_cart = is_in_cart($conn, $_SESSION['user_id'], $book['id']);
            echo "<div class='test-box " . ($in_cart ? "success" : "info") . "'>";
            echo "<h3>3. Cart Status</h3>";
            if ($in_cart) {
                echo "<p style='color: green;'>✓ Book is already in cart</p>";
                echo "<p><a href='cart.php'>View Cart</a></p>";
            } else {
                echo "<p>Book is NOT in cart</p>";
            }
            echo "</div>";
            
            echo "<div class='test-box'>";
            echo "<h3>4. Add to Cart Link Test</h3>";
            $add_url = "php/add-to-cart.php?book_id=" . $book['id'];
            echo "<p>URL: <code>" . $add_url . "</code></p>";
            echo "<p><a href='" . $add_url . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Click to Add to Cart</a></p>";
            echo "</div>";
            
            // Test direct add
            if (isset($_GET['test_direct'])) {
                echo "<div class='test-box'>";
                echo "<h3>5. Direct Add Test Result</h3>";
                $result = add_to_cart($conn, $_SESSION['user_id'], $book['id']);
                if ($result) {
                    echo "<p style='color: green;'>✓ Successfully added to cart!</p>";
                    echo "<p><a href='cart.php'>View Cart</a></p>";
                } else {
                    echo "<p style='color: red;'>✗ Failed to add to cart</p>";
                    echo "<p>Error: " . print_r($conn->errorInfo(), true) . "</p>";
                }
                echo "</div>";
            } else {
                echo "<div class='test-box'>";
                echo "<h3>5. Direct Add Test</h3>";
                echo "<p><a href='?test_direct=1' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Direct Add (No Redirect)</a></p>";
                echo "</div>";
            }
        } else {
            echo "<div class='test-box error'>";
            echo "<h3>Please login first</h3>";
            echo "<p><a href='user-login.php'>Login Here</a></p>";
            echo "</div>";
        }
    } else {
        echo "<div class='test-box error'>";
        echo "<p>No paid books found in database</p>";
        echo "</div>";
    }
    
    echo "<hr>";
    echo "<p><a href='index.php'>Back to Homepage</a></p>";
    echo "<p><a href='debug-cart.php'>Debug Cart</a></p>";
    ?>
</body>
</html>
