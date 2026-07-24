<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "../db_conn.php";
    include "func-validation.php";
    include "func-user.php";

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $user_input = "name=" . urlencode($full_name) . "&email=" . urlencode($email) . "&phone=" . urlencode($phone);

    // Validation
    if (empty($full_name)) {
        header("Location: ../register.php?error=Full name is required&$user_input");
        exit;
    }

    if (empty($email)) {
        header("Location: ../register.php?error=Email is required&$user_input");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../register.php?error=Invalid email format&$user_input");
        exit;
    }

    if (email_exists($conn, $email)) {
        header("Location: ../register.php?error=Email already registered&$user_input");
        exit;
    }

    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        header("Location: ../register.php?error=Phone number must be 10 digits&$user_input");
        exit;
    }

    if (strlen($password) < 6) {
        header("Location: ../register.php?error=Password must be at least 6 characters&$user_input");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: ../register.php?error=Passwords do not match&$user_input");
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$full_name, $email, $phone, $hashed_password]);

    if ($result) {
        $user_id = $conn->lastInsertId();
        
        // Auto-login
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $full_name;
        $_SESSION['user_type'] = 'customer';
        
        header("Location: ../index.php?success=Registration successful! Welcome to Tatvam Publication");
        exit;
    } else {
        header("Location: ../register.php?error=Registration failed. Please try again&$user_input");
        exit;
    }
} else {
    header("Location: ../register.php");
    exit;
}
