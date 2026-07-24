<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../user-login.php");
    exit;
}

include "../db_conn.php";
include "func-cart.php";

$user_id = $_SESSION['user_id'];

if (clear_cart($conn, $user_id)) {
    header("Location: ../cart.php?success=Cart cleared");
    exit;
} else {
    header("Location: ../cart.php?error=Failed to clear cart");
    exit;
}
