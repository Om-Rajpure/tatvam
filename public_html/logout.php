<?php 

session_start();

// Check user type before destroying session
$is_admin = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin';

session_unset();
session_destroy();

// Redirect to appropriate login page
if ($is_admin) {
    header("Location: login.php");
} else {
    header("Location: index.php");
}
exit;