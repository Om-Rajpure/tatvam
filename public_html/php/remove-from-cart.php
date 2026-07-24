<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../user-login.php");
    exit;
}

include "../db_conn.php";
include "func-cart.php";

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$user_id = $_SESSION['user_id'];

if ($book_id <= 0) {
    header("Location: ../cart.php?error=Invalid book");
    exit;
}

if (remove_from_cart($conn, $user_id, $book_id)) {
    header("Location: ../cart.php?success=Item removed from cart");
    exit;
} else {
    header("Location: ../cart.php?error=Failed to remove item");
    exit;
}
