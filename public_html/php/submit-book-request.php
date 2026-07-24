<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../user-login.php");
    exit;
}

include "../db_conn.php";
include "func-book-request.php";
include "func-file-upload.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $author_name = trim($_POST['author_name']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);

    // Validation
    if (empty($title) || empty($author_name) || empty($description) || $category_id == 0) {
        header("Location: ../submit-book.php?error=All fields are required");
        exit;
    }

    // Upload cover
    $cover_allowed = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
    $cover = upload_file($_FILES['cover'], $cover_allowed, '../uploads/cover/');
    
    if ($cover['status'] == 'error') {
        header("Location: ../submit-book.php?error=" . urlencode($cover['data']));
        exit;
    }
    $cover_name = $cover['data'];

    // Upload file
    $file_allowed = ['pdf'];
    $file = upload_file($_FILES['file'], $file_allowed, '../uploads/files/');
    
    if ($file['status'] == 'error') {
        unlink("../uploads/cover/" . $cover_name);
        header("Location: ../submit-book.php?error=" . urlencode($file['data']));
        exit;
    }
    $file_name = $file['data'];

    // Submit request
    $data = [
        'user_id' => $user_id,
        'title' => $title,
        'author_name' => $author_name,
        'description' => $description,
        'category_id' => $category_id,
        'price' => $price,
        'cover' => $cover_name,
        'file' => $file_name
    ];

    if (submit_book_request($conn, $data)) {
        header("Location: ../my-book-requests.php?success=Book submitted successfully! Waiting for admin approval");
    } else {
        header("Location: ../submit-book.php?error=Failed to submit book request");
    }
} else {
    header("Location: ../submit-book.php");
}
