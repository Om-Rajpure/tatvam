<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user-login.php?redirect=index.php&error=Please login to add items to cart");
    exit;
}

include "../db_conn.php";
include "func-cart.php";
include "func-book.php";

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$user_id = $_SESSION['user_id'];

if ($book_id <= 0) {
    header("Location: ../index.php?error=Invalid book ID");
    exit;
}

// Get book details
$book = get_book($conn, $book_id);

if (!$book) {
    header("Location: ../index.php?error=Book not found (ID: $book_id)");
    exit;
}

// Check if already in cart
if (is_in_cart($conn, $user_id, $book_id)) {
    header("Location: ../cart.php?info=Book already in cart");
    exit;
}

// Add to cart
try {
    $result = add_to_cart($conn, $user_id, $book_id);
    if ($result) {
        header("Location: ../cart.php?success=Book added to cart successfully");
        exit;
    } else {
        header("Location: ../index.php?error=Failed to add book to cart - Database error");
        exit;
    }
} catch (Exception $e) {
    header("Location: ../index.php?error=Error: " . urlencode($e->getMessage()));
    exit;
}
