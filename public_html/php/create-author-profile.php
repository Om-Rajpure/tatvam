<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../user-login.php");
    exit;
}

include "../db_conn.php";
include "func-author.php";
include "func-file-upload.php";

// Validate required fields
if (empty($_POST['name']) || empty($_POST['about']) || empty($_POST['qualification'])) {
    header("Location: ../author-registration.php?error=" . urlencode('Full name, about/bio, and qualification are required.'));
    exit;
}

$photo = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $upload_result = upload_file($_FILES['photo'], ['jpg','jpeg','png','gif','webp'], '../uploads/author_photos/');
    if ($upload_result['status'] === 'success') {
        $photo = $upload_result['data'];
    } else {
        header("Location: ../author-registration.php?error=" . urlencode('Photo upload failed: ' . $upload_result['data']));
        exit;
    }
}

$data = [
    'user_id'      => $_SESSION['user_id'],
    'name'         => trim($_POST['name']),
    'photo'        => $photo,
    'about'        => trim($_POST['about']),
    'qualification'=> trim($_POST['qualification']),
    'designation'  => trim($_POST['designation'] ?? ''),
    'organization' => trim($_POST['organization'] ?? ''),
    'contact'      => trim($_POST['contact'] ?? '')
];

if (function_exists('create_author_profile')) {
    $author_id = create_author_profile($conn, $data);
} else {
    // Fallback if the function is not available yet
    $sql = "INSERT INTO authors (name, photo, about, qualification, designation, organization, contact, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $data['name'],
        $data['photo'],
        $data['about'],
        $data['qualification'],
        $data['designation'],
        $data['organization'],
        $data['contact'],
        $data['user_id']
    ]);
    $author_id = $conn->lastInsertId();
}

if ($author_id) {
    header("Location: ../submit-book.php?success=" . urlencode('Author profile created! You can now publish your work.'));
    exit;
} else {
    header("Location: ../author-registration.php?error=" . urlencode('Failed to create author profile. Please try again.'));
    exit;
}
