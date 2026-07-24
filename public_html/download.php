<?php
session_start();
include "db_conn.php";
include "php/func-cart.php";

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$type    = isset($_GET['type']) ? $_GET['type'] : 'full'; // 'preview' or 'full'

if (!$book_id) {
    header("Location: index.php");
    exit;
}

// Get book details
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    die("Book not found.");
}

$is_admin    = (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin');
$is_customer = (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer');
$is_free     = ($book['price'] == 0);

if ($type === 'preview') {
    // Preview PDF — no authentication required
    $preview_file = $book['preview_file'] ?? '';
    if (empty($preview_file)) {
        header("Location: book-detail.php?id=$book_id&error=" . urlencode('No preview available for this item.'));
        exit;
    }
    $file_path = 'uploads/files/' . $preview_file;
} else {
    // Full PDF — requires auth + purchase (or free / admin)
    if ($is_free) {
        $file_path = 'uploads/files/' . $book['file'];
    } elseif ($is_admin) {
        // Admin can always download for review purposes
        $file_path = 'uploads/files/' . $book['file'];
    } elseif ($is_customer && has_purchased($conn, $_SESSION['user_id'], $book_id)) {
        $file_path = 'uploads/files/' . $book['file'];
    } else {
        if (!isset($_SESSION['user_id'])) {
            header("Location: user-login.php?redirect=book-detail.php?id=$book_id");
        } else {
            header("Location: book-detail.php?id=$book_id&error=" . urlencode('Please purchase this item to download the full version.'));
        }
        exit;
    }
}

// Security: prevent directory traversal
$file_path = 'uploads/files/' . basename($type === 'preview' ? ($book['preview_file'] ?? '') : $book['file']);

if (!file_exists($file_path)) {
    die('File not found on server.');
}

// Serve the file
$filename = ($type === 'preview' ? 'Preview_' : '') . basename($book['title']) . '.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
