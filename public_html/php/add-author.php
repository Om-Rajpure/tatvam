<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include "../db_conn.php";
include "func-file-upload.php";

$name = trim($_POST['name'] ?? '');
if (empty($name)) {
    header("Location: ../add-author.php?error=" . urlencode('Author name is required.'));
    exit;
}

$photo = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $upload_result = upload_file($_FILES['photo'], ['jpg','jpeg','png','gif','webp','bmp'], '../uploads/author_photos/');
    if ($upload_result['status'] === 'success') {
        $photo = $upload_result['data'];
    }
}

$sql = "INSERT INTO authors (name, photo, about, qualification, designation, organization, contact)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([
    $name,
    $photo,
    trim($_POST['about'] ?? '') ?: null,
    trim($_POST['qualification'] ?? '') ?: null,
    trim($_POST['designation'] ?? '') ?: null,
    trim($_POST['organization'] ?? '') ?: null,
    trim($_POST['contact'] ?? '') ?: null
]);

header("Location: ../add-author.php?success=" . urlencode('Author added successfully.'));
exit;