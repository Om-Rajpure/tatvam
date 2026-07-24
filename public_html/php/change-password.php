<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../user-login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "../db_conn.php";
    include "func-user.php";

    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $user = get_user($conn, $user_id);

    if (!password_verify($current_password, $user['password'])) {
        header("Location: ../user-profile.php?error=Current password is incorrect");
        exit;
    }

    if (strlen($new_password) < 6) {
        header("Location: ../user-profile.php?error=New password must be at least 6 characters");
        exit;
    }

    if ($new_password !== $confirm_password) {
        header("Location: ../user-profile.php?error=New passwords do not match");
        exit;
    }

    if (change_password($conn, $user_id, $new_password)) {
        header("Location: ../user-profile.php?success=Password changed successfully");
        exit;
    } else {
        header("Location: ../user-profile.php?error=Failed to change password");
        exit;
    }
} else {
    header("Location: ../user-profile.php");
    exit;
}
