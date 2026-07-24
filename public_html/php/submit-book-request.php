<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../user-login.php");
    exit;
}

include "../db_conn.php";
include "func-book-request.php";
include "func-file-upload.php";
@include "func-scanner.php";

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

    // Upload preview PDF (optional)
    $preview_filename = null;
    if (isset($_FILES['preview_file']) && $_FILES['preview_file']['error'] === UPLOAD_ERR_OK) {
        $preview_upload = upload_file($_FILES['preview_file'], ['pdf'], '../uploads/files/');
        if ($preview_upload['status'] === 'success') {
            $preview_filename = $preview_upload['data'];
        }
    }
    
    // Run content scan on the uploaded PDF
    if (function_exists('scan_file')) {
        $scan_result = scan_file('../uploads/files/' . $file_name); 
        if (!$scan_result['pass']) {
            // Scan failed — delete uploaded files and reject
            if (file_exists('../uploads/cover/' . $cover_name)) @unlink('../uploads/cover/' . $cover_name);
            if (file_exists('../uploads/files/' . $file_name)) @unlink('../uploads/files/' . $file_name);
            if ($preview_filename && file_exists('../uploads/files/' . $preview_filename)) @unlink('../uploads/files/' . $preview_filename);
            header("Location: ../submit-book.php?error=" . urlencode('Content scan failed: ' . $scan_result['report']));
            exit;
        }
    }

    // Submit request
    $data = [
        'user_id' => $user_id,
        'title' => $title,
        'author_name' => $author_name,
        'description' => $description,
        'category_id' => $category_id,
        'price' => $price,
        'cover' => $cover_name,
        'file' => $file_name,
        'content_type' => $_POST['content_type'] ?? 'book',
        'isbn'         => trim($_POST['isbn'] ?? '') ?: null,
        'doi'          => trim($_POST['doi'] ?? '') ?: null,
        'pages'        => intval($_POST['pages'] ?? 0) ?: null,
        'format'       => $_POST['format'] ?? 'eBook',
        'preview_file' => $preview_filename
    ];

    if (submit_book_request($conn, $data)) {
        header("Location: ../my-book-requests.php?success=Book submitted successfully! Waiting for admin approval");
    } else {
        header("Location: ../submit-book.php?error=Failed to submit book request");
    }
} else {
    header("Location: ../submit-book.php");
}
