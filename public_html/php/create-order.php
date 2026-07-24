<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../user-login.php");
    exit;
}

include "../db_conn.php";
include "func-cart.php";
include "func-order.php";
include "func-user.php";

$user_id = $_SESSION['user_id'];

// Validate cart not empty
$cart_count = get_cart_count($conn, $user_id);
if ($cart_count == 0) {
    header("Location: ../cart.php?error=Your cart is empty");
    exit;
}

// Create order
$result = create_order($conn, $user_id);

if ($result['status'] == 'success') {
    $_SESSION['order_id'] = $result['order_id'];
    $_SESSION['order_number'] = $result['order_number'];
    
    // Redirect to payment page
    header("Location: ../payment.php?success=Order created successfully");
} else {
    header("Location: ../checkout.php?error=Failed to create order: " . urlencode($result['message']));
}
exit;
