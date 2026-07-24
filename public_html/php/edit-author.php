<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include "../db_conn.php";
include "func-file-upload.php";

$id = intval($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
if (!$id || empty($name)) {
    header("Location: ../admin.php?error=" . urlencode('Invalid request.'));
    exit;
}

// Handle photo upload (keep existing if no new file)
$photo_sql = '';
$photo_params = [];
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $upload_result = upload_file($_FILES['photo'], ['jpg','jpeg','png','gif','webp','bmp'], '../uploads/author_photos/');
    if ($upload_result['status'] === 'success') {
        $photo_sql = ', photo = ?';
        $photo_params = [$upload_result['data']];
    }
}

$params = [
    $name,
    trim($_POST['about'] ?? '') ?: null,
    trim($_POST['qualification'] ?? '') ?: null,
    trim($_POST['designation'] ?? '') ?: null,
    trim($_POST['organization'] ?? '') ?: null,
    trim($_POST['contact'] ?? '') ?: null
];
array_push($params, ...$photo_params);
$params[] = $id;

$sql = "UPDATE authors SET name = ?, about = ?, qualification = ?, designation = ?, organization = ?, contact = ?" . $photo_sql . " WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute($params);

header("Location: ../edit-author.php?id=$id&success=" . urlencode('Author updated successfully.'));
exit;