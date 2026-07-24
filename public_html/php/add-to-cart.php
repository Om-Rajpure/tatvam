<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    if (strpos($redirect, '://') !== false || strpos($redirect, '//') === 0) {
        $redirect = 'index.php';
    }
    header("Location: ../user-login.php?redirect=" . urlencode($redirect) . "&error=Please login to add items to cart");
    exit;
}

include "../db_conn.php";
include "func-cart.php";
include "func-book.php";

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$user_id = $_SESSION['user_id'];

if ($book_id <= 0) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    if (strpos($redirect, '://') !== false || strpos($redirect, '//') === 0) {
        $redirect = 'index.php';
    }
    header("Location: ../" . ltrim($redirect, '/') . (strpos($redirect, '?') !== false ? '&' : '?') . "error=Invalid book ID");
    exit;
}

// Get book details
$book = get_book($conn, $book_id);

if (!$book) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    if (strpos($redirect, '://') !== false || strpos($redirect, '//') === 0) {
        $redirect = 'index.php';
    }
    header("Location: ../" . ltrim($redirect, '/') . (strpos($redirect, '?') !== false ? '&' : '?') . "error=Book not found (ID: $book_id)");
    exit;
}

// Check if already in cart
if (is_in_cart($conn, $user_id, $book_id)) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'cart.php';
    if (strpos($redirect, '://') !== false || strpos($redirect, '//') === 0) {
        $redirect = 'cart.php';
    }
    header("Location: ../" . ltrim($redirect, '/') . (strpos($redirect, '?') !== false ? '&' : '?') . "info=Book already in cart");
    exit;
}

// Add to cart
try {
    $result = add_to_cart($conn, $user_id, $book_id);
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'cart.php';
    if (strpos($redirect, '://') !== false || strpos($redirect, '//') === 0) {
        $redirect = 'cart.php';
    }
    if ($result) {
        header("Location: ../" . ltrim($redirect, '/') . (strpos($redirect, '?') !== false ? '&' : '?') . "success=" . urlencode("Book added to cart successfully"));
        exit;
    } else {
        header("Location: ../" . ltrim($redirect, '/') . (strpos($redirect, '?') !== false ? '&' : '?') . "error=" . urlencode("Failed to add book to cart - Database error"));
        exit;
    }
} catch (Exception $e) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    if (strpos($redirect, '://') !== false || strpos($redirect, '//') === 0) {
        $redirect = 'index.php';
    }
    header("Location: ../" . ltrim($redirect, '/') . (strpos($redirect, '?') !== false ? '&' : '?') . "error=Error: " . urlencode($e->getMessage()));
    exit;
}
