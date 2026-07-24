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
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);

    if (empty($full_name)) {
        header("Location: ../user-profile.php?error=Full name is required");
        exit;
    }

    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        header("Location: ../user-profile.php?error=Phone number must be 10 digits");
        exit;
    }

    if (update_user($conn, $user_id, $full_name, $phone)) {
        $_SESSION['user_name'] = $full_name;
        header("Location: ../user-profile.php?success=Profile updated successfully");
        exit;
    } else {
        header("Location: ../user-profile.php?error=Failed to update profile");
        exit;
    }
} else {
    header("Location: ../user-profile.php");
    exit;
}
