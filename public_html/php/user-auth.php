<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "../db_conn.php";
    include "func-user.php";

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($email)) {
        header("Location: ../user-login.php?error=Email is required");
        exit;
    }

    if (empty($password)) {
        header("Location: ../user-login.php?error=Password is required");
        exit;
    }

    // Get user by email
    $user = get_user_by_email($conn, $email);

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_type'] = 'customer';
        
        // Redirect to intended page or homepage
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
        header("Location: ../$redirect");
        exit;
    } else {
        header("Location: ../user-login.php?error=Invalid email or password");
        exit;
    }
} else {
    header("Location: ../user-login.php");
    exit;
}
