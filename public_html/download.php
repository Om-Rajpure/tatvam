<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit;
}

include "db_conn.php";
include "php/func-cart.php";

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$user_id = $_SESSION['user_id'];

// Get book details
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    die("Book not found");
}

// Check if book is free or user has purchased it
if ($book['price'] > 0 && !has_purchased($conn, $user_id, $book_id)) {
    die("You need to purchase this book first");
}

// File path
$file_path = "uploads/files/" . $book['file'];

if (!file_exists($file_path)) {
    die("File not found");
}

// Set headers for download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($book['file']) . '"');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
